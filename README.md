<p align="center">
    <a href="https://github.com/yii1tech" target="_blank">
        <img src="https://avatars.githubusercontent.com/u/134691944" height="100px">
    </a>
    <h1 align="center">Yii1 Asynchronous Shell Command Runner</h1>
    <br>
</p>

This extension provides asynchronous shell command runner for Yii1.

For license information check the [LICENSE](LICENSE.md)-file.

[![Latest Stable Version](https://img.shields.io/packagist/v/yii1tech/async-cmd.svg)](https://packagist.org/packages/yii1tech/async-cmd)
[![Total Downloads](https://img.shields.io/packagist/dt/yii1tech/async-cmd.svg)](https://packagist.org/packages/yii1tech/async-cmd)
[![Build Status](https://github.com/yii1tech/async-cmd/workflows/build/badge.svg)](https://github.com/yii1tech/async-cmd/actions)


Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist yii1tech/async-cmd
```

or add

```json
"yii1tech/async-cmd": "*"
```

to the "require" section of your composer.json.


Usage
-----

This extension provides asynchronous shell command runner for Yii1.

Application configuration example:

```php
<?php

return [
    'components' => [
        \yii1tech\async\cmd\CommandDispatcher::class => [
            'class' => \yii1tech\async\cmd\CommandDispatcher::class,
        ],
        // ...
    ],
    // ...
];
```

Usage example:

```php
<?php

use yii1tech\async\cmd\CommandDispatcher;

/** @var CommandDispatcher $dispatcher */
$dispatcher = Yii::app()->getComponent(CommandDispatcher::class);

// run Yii console command:
$dispatcher->create()
    ->yiic(StatsCommand::class, 'generate', ['date' => '2023-08-29']);

// run arbitrary console command:
$dispatcher->create()
    ->external('curl', [
        '-X' => 'POST',
        '-d' => 'param1=value1&param2=value2',
        'http://example.com/api/notify',
    ]);
```