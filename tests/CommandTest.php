<?php

namespace yii1tech\async\cmd\test;

use yii1tech\async\cmd\Command;

class CommandTest extends TestCase
{
    public function testSetup(): void
    {
        $command = new Command();

        $commandClass = 'FooCommand';
        $command->setCommandClass($commandClass);
        $this->assertSame($commandClass, $command->getCommandClass());
    }
}