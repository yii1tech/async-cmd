<?php

namespace yii1tech\async\cmd\test;

use yii1tech\async\cmd\ArrayCommandRunner;
use yii1tech\async\cmd\Command;
use yii1tech\async\cmd\CommandDispatcher;
use yii1tech\async\cmd\LinuxCommandRunner;

class CommandDispatcherTest extends TestCase
{
    public function testCreateCommand(): void
    {
        $dispatcher = new CommandDispatcher();

        $command = $dispatcher->create();
        $command->isDispatched = true;

        $this->assertTrue($command instanceof Command);
        $this->assertSame($dispatcher, $command->getDispatcher());
    }

    public function testCreateCommandByConfig(): void
    {
        $dispatcher = new CommandDispatcher();
        $dispatcher->commandConfig = [
            'outputLog' => '/path/to/file.log',
            'isDispatched' => true,
        ];

        $command = $dispatcher->create();

        $this->assertTrue($command instanceof Command);
        $this->assertSame($dispatcher, $command->getDispatcher());
        $this->assertSame($dispatcher->commandConfig['outputLog'], $command->getOutputLog());
    }

    public function testSetupCommandRunner(): void
    {
        $dispatcher = new CommandDispatcher();

        $runner = new ArrayCommandRunner();
        $dispatcher->setCommandRunner($runner);
        $this->assertSame($runner, $dispatcher->getCommandRunner());

        $dispatcher->setCommandRunner([
            'class' => ArrayCommandRunner::class,
        ]);
        $this->assertTrue($dispatcher->getCommandRunner() instanceof ArrayCommandRunner);
        $this->assertNotSame($runner, $dispatcher->getCommandRunner());
    }

    public function testGetDefaultCommandRunner(): void
    {
        $dispatcher = new CommandDispatcher();

        $runner = $dispatcher->getCommandRunner();
        $this->assertTrue($runner instanceof LinuxCommandRunner);
    }

    /**
     * @depends testSetupCommandRunner
     */
    public function testDispatch(): void
    {
        $dispatcher = new CommandDispatcher();

        $runner = new ArrayCommandRunner();
        $dispatcher->setCommandRunner($runner);

        $command = $dispatcher->create()
            ->setBinPath('/user/bin/foo')
            ->dispatch();

        $this->assertSame($command, $runner->getLastCommand());
        $this->assertTrue($command->isDispatched);
    }

    /**
     * @depends testDispatch
     */
    public function testAutoDispatch(): void
    {
        $dispatcher = new CommandDispatcher();

        $runner = new ArrayCommandRunner();
        $dispatcher->setCommandRunner($runner);

        $dispatcher->create()
            ->setAutoDispatch(true)
            ->setBinPath('/user/bin/foo');

        $command = $runner->getLastCommand();

        $this->assertTrue($command instanceof Command);
        $this->assertTrue($command->isDispatched);
    }
}