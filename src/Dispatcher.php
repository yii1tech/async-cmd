<?php

namespace yii1tech\async\cmd;

use CApplicationComponent;
use Yii;

/**
 * @author Paul Klimov <klimov.paul@gmail.com>
 * @since 1.0
 */
class Dispatcher extends CApplicationComponent
{
    /**
     * @var array Yii-style array configuration for the command instances.
     * @see createCommand()
     */
    public $commandConfig = [];

    public function createCommand(): Command
    {
        $config = array_merge([
            'class' => Command::class,
        ], $this->commandConfig);

        /** @var Command $command */
        $command = Yii::createComponent($config);
        $command->setDispatcher($this);

        return $command;
    }
}