<?php

namespace yii1tech\async\cmd;

use CApplicationComponent;
use InvalidArgumentException;
use Yii;

/**
 * LinuxCommandRunner relies on Linux command line utility to run command without waiting its result.
 *
 * @author Paul Klimov <klimov.paul@gmail.com>
 * @since 1.0
 */
class LinuxCommandRunner extends CApplicationComponent implements CommandRunnerContract
{
    /**
     * @var string path to PHP binary, by default 'php' is used assuming it is available as a shell command.
     */
    public $phpBinPath = 'php';
    /**
     * @var string|null path to Yii console application entry script. If not set 'yiic' at application base path will be used.
     */
    public $yiicPath;

    /**
     * {@inheritdoc}
     */
    public function run(Command $command): void
    {
        $binPath = $command->getBinPath();
        if (empty($binPath)) {
            $commandClass = $command->getCommandClass();
            if (empty($commandClass)) {
                throw new InvalidArgumentException('Command to be executed should be set with either "binPath" or "commandClass".');
            }

            $binPath = $this->phpBinPath;

            $yiicPath = $this->yiicPath ?? (Yii::app()->getBasePath() . DIRECTORY_SEPARATOR . 'yiic');

            $binPath .= ' ' . $yiicPath;
        }

        $paramString = $this->composeParamsString($command);
        if (!empty($paramString)) {
            $binPath .= ' ' . $paramString;
        }

        $binPath .= ' 2>&1';

        $logPath = $command->getOutputLog();
        if (!empty($logPath)) {
            $binPath .= ' >> ' . $logPath;
        }

        $binPath .= ' &';

        system($binPath);
    }

    /**
     * Composes parameters fragment of the console command string.
     *
     * @param \yii1tech\async\cmd\Command $command command instance.
     * @return string parameters fragment of the console command string.
     */
    protected function composeParamsString(Command $command): string
    {
        $isYiic = empty($command->getBinPath());

        $paramsParts = [];
        foreach ($command->getParams() as $key => $value) {
            if (is_array($value) || is_object($value)) {
                $value = json_encode($value);
            }

            $value = escapeshellarg($value);

            if (is_int($key)) {
                $paramsParts[] = $value;
            } elseif ($isYiic) {
                $paramsParts[] = '--' . trim($key, '-') . '=' . $value;
            } else {
                $paramsParts[] = $key . ' ' . $value;
            }
        }

        if (empty($paramsParts)) {
            return '';
        }

        return implode(' ', $paramsParts);
    }
}