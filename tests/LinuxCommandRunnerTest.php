<?php

namespace yii1tech\async\cmd\test;

use FooCommand;
use Yii;
use yii1tech\async\cmd\CommandDispatcher;
use yii1tech\async\cmd\LinuxCommandRunner;

class LinuxCommandRunnerTest extends TestCase
{
    /**
     * @return \yii1tech\async\cmd\CommandDispatcher test command dispatcher.
     */
    protected function createCommandDispatcher(): CommandDispatcher
    {
        $dispatcher = new CommandDispatcher();
        $dispatcher->setCommandRunner([
            'class' => LinuxCommandRunner::class,
            'yiicPath' => __DIR__ . '/support/yiic',
        ]);

        return $dispatcher;
    }

    /**
     * Creates a pause, allowing async command to execute.
     *
     * @return void
     */
    protected function wait(): void
    {
        usleep(100000);
    }

    public function testRunExternal(): void
    {
        $dispatcher = $this->createCommandDispatcher();

        $logFilename = Yii::app()->getRuntimePath() . '/external.log';

        $dispatcher->create()
            ->setBinPath('ls -al')
            ->setParams([__DIR__])
            ->setOutputLog($logFilename)
            ->dispatch();

        $this->wait();

        $this->assertTrue(file_exists($logFilename));

        $logContent = file_get_contents($logFilename);
        $this->assertStringContainsString(basename(__FILE__), $logContent);
    }

    public function testRunYiic(): void
    {
        $dispatcher = $this->createCommandDispatcher();

        $logFilename = Yii::app()->getRuntimePath() . '/yiic.log';

        $dispatcher->create()
            ->yiic(FooCommand::class)
            ->setOutputLog($logFilename)
            ->dispatch();

        $this->wait();

        $this->assertTrue(file_exists($logFilename));

        $logContent = file_get_contents($logFilename);
        $this->assertStringContainsString('FooCommand::actionIndex', $logContent);
    }

    /**
     * @depends testRunYiic
     */
    public function testRunYiicWithNamedParams(): void
    {
        $dispatcher = $this->createCommandDispatcher();

        $logFilename = Yii::app()->getRuntimePath() . '/yiic-named.log';

        $dispatcher->create()
            ->yiic(FooCommand::class, 'named', ['input' => 123])
            ->setOutputLog($logFilename)
            ->dispatch();

        $this->wait();

        $this->assertTrue(file_exists($logFilename));

        $logContent = file_get_contents($logFilename);
        $this->assertStringContainsString('string 123', $logContent);
    }

    /**
     * @depends testRunYiic
     */
    public function testRunYiicWithUnnamedArgs(): void
    {
        $dispatcher = $this->createCommandDispatcher();

        $logFilename = Yii::app()->getRuntimePath() . '/yiic-args.log';

        $dispatcher->create()
            ->yiic(FooCommand::class, 'args', ['bar', 123])
            ->setOutputLog($logFilename)
            ->dispatch();

        $this->wait();

        $this->assertTrue(file_exists($logFilename));

        $logContent = file_get_contents($logFilename);
        $this->assertStringContainsString('bar', $logContent);
        $this->assertStringContainsString('123', $logContent);
    }

    /**
     * @depends testRunYiicWithNamedParams
     */
    public function testRunLongTimeCommand(): void
    {
        $dispatcher = $this->createCommandDispatcher();

        $hash = uniqid();

        $dispatcher->create()
            ->yiic(FooCommand::class, 'long', ['hash' => $hash, 'sleep' => 0])
            ->dispatch();

        $this->wait();

        $filename = Yii::app()->getRuntimePath() . "/{$hash}.tmp";

        $this->assertTrue(file_exists($filename));

        $hash = uniqid();

        $dispatcher->create()
            ->yiic(FooCommand::class, 'long', ['hash' => $hash, 'sleep' => 2])
            ->dispatch();

        $this->wait();

        $filename = Yii::app()->getRuntimePath() . "/{$hash}.tmp";

        $this->assertFalse(file_exists($filename));
    }
}