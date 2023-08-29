<?php

class FooCommand extends CConsoleCommand
{
    public function actionIndex()
    {
        echo __METHOD__;
    }

    public function actionNamed($input)
    {
        echo gettype($input) . ' ' . print_r($input, true);
    }

    public function actionArgs(array $args)
    {
        echo print_r($args, true);
    }

    public function actionLong($hash, $sleep = 0)
    {
        sleep($sleep);

        $fileName = Yii::app()->getRuntimePath() . DIRECTORY_SEPARATOR . $hash . '.tmp';

        try {
            file_put_contents($fileName, $hash);
        } catch (\Throwable $e) {
            // shutdown exception
        }
    }
}