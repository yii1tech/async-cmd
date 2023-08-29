<?php

namespace yii1tech\async\cmd\test;

use yii1tech\async\cmd\Command;
use yii1tech\async\cmd\CommandDispatcher;

class CommandTest extends TestCase
{
    /**
     * Creates test command instance.
     *
     * @return \yii1tech\async\cmd\Command command instance.
     */
    protected function createCommand(): Command
    {
        $command = new Command();
        $command->isDispatched = true;

        return $command;
    }

    public function testSetup(): void
    {
        $command = $this->createCommand();

        $commandClass = 'FooCommand';
        $command->setCommandClass($commandClass);
        $this->assertSame($commandClass, $command->getCommandClass());

        $commandAction = 'foo';
        $command->setCommandAction($commandAction);
        $this->assertSame($commandAction, $command->getCommandAction());

        $binPath = '/user/bin/php';
        $command->setBinPath($binPath);
        $this->assertSame($binPath, $command->getBinPath());

        $params = ['foo', 'bar'];
        $command->setParams($params);
        $this->assertSame($params, $command->getParams());

        $outputLog = '/path/to/file.log';
        $command->setOutputLog($outputLog);
        $this->assertSame($outputLog, $command->getOutputLog());

        $dispatcher = new CommandDispatcher();
        $command->setDispatcher($dispatcher);
        $this->assertSame($dispatcher, $command->getDispatcher());
    }

    public function testSetupYiic(): void
    {
        $command = $this->createCommand();

        $class = 'FooCommand';
        $action = 'bar';
        $params = ['param1', 'param2'];

        $command->yiic($class, $action, $params);

        $this->assertSame($class, $command->getCommandClass());
        $this->assertSame($action, $command->getCommandAction());
        $this->assertSame($params, $command->getParams());
    }

    public function testSetupExternal(): void
    {
        $command = $this->createCommand();

        $binPath = '/user/bin/foo';
        $params = ['param1', 'param2'];

        $command->external($binPath, $params);

        $this->assertSame($binPath, $command->getBinPath());
        $this->assertSame($params, $command->getParams());
    }
}