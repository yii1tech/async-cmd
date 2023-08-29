<?php

namespace yii1tech\async\cmd;

use CComponent;

/**
 * Command represents particular shell command to be executed.
 *
 * @author Paul Klimov <klimov.paul@gmail.com>
 * @since 1.0
 */
class Command extends CComponent
{
    /**
     * @var string|null class name of the Yii console command to be executed.
     */
    private $_commandClass;
    /**
     * @var string|null name of the Yii console command action to be executed.
     */
    private $_commandAction;
    /**
     * @var string|null path to the console binary, which should be executed.
     */
    private $_binPath;
    /**
     * @var array list of shell arguments, which should be passed to the command.
     */
    private $_params = [];
    /**
     * @var string|null file or stream, to which the command output should be redirected.
     */
    private $_outputLog;
    /**
     * @var \yii1tech\async\cmd\Dispatcher related dispatcher.
     */
    private $_dispatcher;
    /**
     * @var bool whether the command should be automatically dispatched, once it is out of program scope (e.g. on destruct).
     */
    private $_autoDispatch = true;

    /**
     * @return string|null class name of the Yii console command to be executed.
     */
    public function getCommandClass(): ?string
    {
        return $this->_commandClass;
    }

    /**
     * @param string $commandClass class name of the Yii console command to be executed.
     * @return static self reference.
     */
    public function setCommandClass(string $commandClass): self
    {
        $this->_commandClass = $commandClass;

        return $this;
    }

    /**
     * @return string|null name of the Yii console command action to be executed.
     */
    public function getCommandAction(): ?string
    {
        return $this->_commandAction;
    }

    /**
     * @param string $commandAction name of the Yii console command action to be executed.
     * @return static self reference.
     */
    public function setCommandAction(string $commandAction): self
    {
        $this->_commandAction = $commandAction;

        return $this;
    }

    /**
     * @return string|null path to the console binary, which should be executed.
     */
    public function getBinPath(): ?string
    {
        return $this->_binPath;
    }

    /**
     * @param string $binPath path to the console binary, which should be executed.
     * @return static self reference.
     */
    public function setBinPath(string $binPath): self
    {
        $this->_binPath = $binPath;

        return $this;
    }

    /**
     * @return array<int|string, mixed> list of arguments, which should be applied to the console command.
     */
    public function getParams(): array
    {
        return $this->_params;
    }

    /**
     * @param array<int|string, mixed> $params list of arguments, which should be applied to the console command.
     * @return static self reference.
     */
    public function setParams(array $params): self
    {
        $this->_params = $params;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getOutputLog(): ?string
    {
        return $this->_outputLog;
    }

    /**
     * @param string|null $outputLog
     * @return static self reference.
     */
    public function setOutputLog(string $outputLog): self
    {
        $this->_outputLog = $outputLog;

        return $this;
    }

    /**
     * @return \yii1tech\async\cmd\Dispatcher related dispatcher.
     */
    public function getDispatcher(): Dispatcher
    {
        return $this->_dispatcher;
    }

    /**
     * @param \yii1tech\async\cmd\Dispatcher $dispatcher dispatcher, which should be used to execute this command.
     * @return static self reference.
     */
    public function setDispatcher(Dispatcher $dispatcher): self
    {
        $this->_dispatcher = $dispatcher;

        return $this;
    }

    /**
     * @return bool whether the command should be automatically dispatched, once it is out of program scope (e.g. on destruct).
     */
    public function getAutoDispatch(): bool
    {
        return $this->_autoDispatch;
    }

    /**
     * @param bool $autoDispatch whether the command should be automatically dispatched, once it is out of program scope (e.g. on destruct).
     * @return static self reference.
     */
    public function setAutoDispatch(bool $autoDispatch): self
    {
        $this->_autoDispatch = $autoDispatch;

        return $this;
    }

    public function yiic(string $class, ?string $action = null, array $params = []): self
    {
        $this->setCommandClass($class);
        if ($action !== null) {
            $this->setCommandAction($action);
        }
        $this->setParams($params);

        return $this;
    }

    public function external(string $binPath, array $params = []): self
    {
        return $this->setBinPath($binPath)
            ->setParams($params);
    }
}