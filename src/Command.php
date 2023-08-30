<?php

namespace yii1tech\async\cmd;

use CComponent;

/**
 * Command represents particular shell command to be executed.
 *
 * Do not instantiate this class on your own, use {@see \yii1tech\async\cmd\CommandDispatcher::create()} instead.
 *
 * @property string|null $commandClass class name of the Yii console command to be executed.
 * @property string|null $commandAction name of the Yii console command action to be executed.
 * @property string|null $binPath path to the console binary, which should be executed.
 * @property array<int|string, mixed> $params list of shell arguments, which should be passed to the command.
 * @property string|null $outputLog file or stream, to which the command output should be redirected.
 * @property \yii1tech\async\cmd\CommandDispatcher $dispatcher the related dispatcher.
 * @property bool $autoDispatch whether the command should be automatically dispatched, once it is out of program scope (e.g. on destruct).
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
     * @var array<int|string, mixed> list of shell arguments, which should be passed to the command.
     */
    private $_params = [];
    /**
     * @var string|null file or stream, to which the command output should be redirected.
     */
    private $_outputLog;
    /**
     * @var \yii1tech\async\cmd\CommandDispatcher the related dispatcher.
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
     *
     * Param values will be automatically sanitized using {@see escapeshellarg()}.
     *
     * For the external command parameter name should be specified in full, including possible prefixing '-' symbols.
     * For example:
     *
     * ```
     * [
     *     '-X' => 'POST',
     *     '-d' => 'param1=value1&param2=value2',
     *     'http://example.com/api/notify',
     * ]
     * ```
     *
     * For the Yii console there is no need to add '-' symbols - it will be performed automatically.
     * For example:
     *
     * ```
     * [
     *     'interactive' => '0',
     *     'foo' => 'bar',
     *     'unnamed-arg-value',
     * ]
     * ```
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
     * @see setParams()
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
     * @see setParams()
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