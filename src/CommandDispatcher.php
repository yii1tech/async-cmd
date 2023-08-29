<?php

namespace yii1tech\async\cmd;

use CApplicationComponent;
use InvalidArgumentException;
use Yii;

/**
 * CommandDispatcher manages console command execution in asynchronous way, without waiting for its result.
 *
 * @author Paul Klimov <klimov.paul@gmail.com>
 * @since 1.0
 */
class CommandDispatcher extends CApplicationComponent
{
    /**
     * @var array Yii-style array configuration for the command instances.
     * @see create()
     * @see \yii1tech\async\cmd\Command
     */
    public $commandConfig = [];
    /**
     * @var \yii1tech\async\cmd\CommandRunnerContract|array command runner.
     */
    private $_commandRunner = [];

    /**
     * @return \yii1tech\async\cmd\CommandRunnerContract
     */
    public function getCommandRunner(): CommandRunnerContract
    {
        if (!is_object($this->_commandRunner)) {
            $this->_commandRunner = Yii::createComponent(array_merge([
                'class' => LinuxCommandRunner::class,
            ], $this->_commandRunner));
        }

        return $this->_commandRunner;
    }

    /**
     * @param \yii1tech\async\cmd\CommandRunnerContract|array $commandRunner
     * @return static self reference.
     */
    public function setCommandRunner($commandRunner): self
    {
        if (!$commandRunner instanceof CommandRunnerContract && !is_array($commandRunner)) {
            throw new InvalidArgumentException('"' . get_class($this) . '::$commandRunner" must be instance of "' . CommandRunnerContract::class . '" or its array configuration.');
        }

        $this->_commandRunner = $commandRunner;

        return $this;
    }

    /**
     * Creates new async command instance.
     *
     * @return \yii1tech\async\cmd\Command new async command instance.
     */
    public function create(): Command
    {
        $config = array_merge([
            'class' => Command::class,
        ], $this->commandConfig);

        /** @var Command $command */
        $command = Yii::createComponent($config);
        $command->setDispatcher($this);

        return $command;
    }

    /**
     * Dispatches command for execution in asynchronous way.
     *
     * @param \yii1tech\async\cmd\Command $command command to be dispatched.
     * @return static self reference.
     */
    public function dispatch(Command $command): self
    {
        $this->getCommandRunner()
            ->run($command);

        $command->isDispatched = true;

        return $this;
    }
}