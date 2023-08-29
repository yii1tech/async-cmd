<?php

// ensure we get report on all possible php errors
error_reporting(-1);

define('YII_DEBUG', true);

require_once(__DIR__ . '/../../vendor/autoload.php');

$config = [
    'name' => 'Test Support Console Application',
    'basePath' => __DIR__,
    'runtimePath' => __DIR__ . '/../runtime',
];

require_once(__DIR__ . '/../../vendor/yiisoft/yii/framework/yiic.php');
