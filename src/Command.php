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
     * @var bool whether this command has been already dispatched.
     */
    public $isDispatched = false;
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
     * @var \yii1tech\async\cmd\CommandDispatcher related dispatcher.
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
     * Sets parameters for the console command execution.
     * For example:
     *
     * ```
     * [
     *     'interactive' => '0',
     *     'foo' => 'bar',
     * ]
     * ```
     *
     * Parameter values will be automatically sanitized.
     *
     * @param array<int|string, mixed> $params list of parameters, which should be applied to the console command.
     * @return static self reference.
     */
    public function setParams(array $params): self
    {
        $this->_params = $params;

        return $this;
    }

    /**
     * @return string|null file or stream, to which the command output should be redirected.
     */
    public function getOutputLog(): ?string
    {
        return $this->_outputLog;
    }

    /**
     * @param string $outputLog file or stream, to which the command output should be redirected.
     * @return static self reference.
     */
    public function setOutputLog(string $outputLog): self
    {
        $this->_outputLog = $outputLog;

        return $this;
    }

    /**
     * @return \yii1tech\async\cmd\CommandDispatcher related dispatcher.
     */
    public function getDispatcher(): CommandDispatcher
    {
        return $this->_dispatcher;
    }

    /**
     * @param \yii1tech\async\cmd\CommandDispatcher $dispatcher dispatcher, which should be used to execute this command.
     * @return static self reference.
     */
    public function setDispatcher(CommandDispatcher $dispatcher): self
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

    /**
     * Configures command to run Yii console application command.
     *
     * @param string $class console command class name.
     * @param string|null $action console command action
     * @param array $params list of parameters, which should be applied to the console command.
     * @return static self reference.
     */
    public function yiic(string $class, ?string $action = null, array $params = []): self
    {
        $this->setCommandClass($class);
        if ($action !== null) {
            $this->setCommandAction($action);
        }
        $this->setParams($params);

        return $this;
    }

    /**
     * Configures command to run external utility in the system.
     *
     * @param string $binPath path to the console binary, which should be executed.
     * @param array $params list of parameters, which should be applied to the console command.
     * @return static self reference.
     */
    public function external(string $binPath, array $params = []): self
    {
        return $this->setBinPath($binPath)
            ->setParams($params);
    }

    /**
     * Dispatches this command for an asynchronous execution, without waiting for its result.
     *
     * @return static self reference.
     */
    public function dispatch(): self
    {
        $this->getDispatcher()->dispatch($this);

        $this->isDispatched = true;

        return $this;
    }

    /**
     * Destructor.
     * Automatically dispatches pending command, if {@see $autoDispatch} is enabled.
     */
    public function __destruct()
    {
        if ($this->getAutoDispatch() && !$this->isDispatched) {
            $this->dispatch();
        }
    }
}