<?php

namespace yii1tech\async\cmd;

/**
 * ArrayCommandRunner does not execute given command, instead it stashes them into the internal array.
 *
 * This class can be useful in unit tests.
 *
 * @author Paul Klimov <klimov.paul@gmail.com>
 * @since 1.0
 */
class ArrayCommandRunner extends \CApplicationComponent implements CommandRunnerContract
{
    /**
     * @var \yii1tech\async\cmd\Command[] commands passed to `run()`.
     */
    public $commands = [];

    /**
     * {@inheritdoc}
     */
    public function run(Command $command): void
    {
        $this->commands[] = $command;
    }

    /**
     * Returns the last command sent for execution.
     *
     * @return \yii1tech\async\cmd\Command|null command instance, `null` if there is no command.
     */
    public function getLastCommand(): ?Command
    {
        if (empty($this->commands)) {
            return null;
        }

        $commands = $this->commands;
        return array_pop($commands);
    }

    /**
     * Flushes commands log array.
     *
     * @return void
     */
    public function flush(): void
    {
        $this->commands = [];
    }
}