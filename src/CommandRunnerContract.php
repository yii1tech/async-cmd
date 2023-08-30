<?php

namespace yii1tech\async\cmd;

/**
 * CommandRunnerContract represents entity which performs actual execution of command in asynchronous way.
 *
 * @see \yii1tech\async\cmd\LinuxCommandRunner
 *
 * @author Paul Klimov <klimov.paul@gmail.com>
 * @since 1.0
 */
interface CommandRunnerContract
{
    /**
     * Executes given command in asynchronous way, without waiting for its result.
     *
     * @param \yii1tech\async\cmd\Command $command command to be executed.
     * @return void
     */
    public function run(Command $command): void;
}