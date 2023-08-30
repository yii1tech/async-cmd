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
It relies on Linux command line utility to run command without waiting its result.
It allows running both Yii console commands and arbitrary external commands.

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
// executes: `php /path/to/project/yiic stats generate --date='2023-08-29'`
$dispatcher->create()
    ->yiic(StatsCommand::class, 'generate', ['date' => '2023-08-29']); 

// run arbitrary console command:
// executes: `curl -X POST -d 'param1=value1&param2=value2' http://example.com/api/notify`
$dispatcher->create()
    ->external('curl', [
        '-X' => 'POST',
        '-d' => 'param1=value1&param2=value2',
        'http://example.com/api/notify',
    ]);
```

Please refer to [\yii1tech\async\cmd\Command](src/Command.php) for more details about command options specification.


### Logging <span id="logging"></span>

Since commands are executed in the asynchronous way it is hard to control whether they were successful or ended with an error.
You may easily add logging of the executed command output to the file using `\yii1tech\async\cmd\Command::setOutputLog()`.
For example:

```php
<?php

use yii1tech\async\cmd\CommandDispatcher;

/** @var CommandDispatcher $dispatcher */
$dispatcher = Yii::app()->getComponent(CommandDispatcher::class);

$dispatcher->create()
    ->yiic(StatsCommand::class, 'generate', ['date' => '2023-08-29'])
    ->setOutputLog(Yii::app()->getRuntimePath() . '/stats-generate.log');
```

Also, actual shell commands being executed are logged as 'info' under category 'yii1tech.async-cmd'.
You may catch them using following log route:

```php
<?php

return [
    'components' => [
        'log' => [
            'class' => \CLogRouter::class,
            'routes' => [
                'fileRoute' => [
                    'class' => \CFileLogRoute::class,
                    'logFile' => 'async-cmd.log',
                    'levels' => 'info',
                    'categories' => 'yii1tech.async-cmd',
                ],
            ],
            // ...
        ],
        // ...
    ],
    // ...
];
```


### Writing unit tests <span id="writing-unit-tests"></span>

Testing of asynchronous flows are troublesome. However, you can handle this using `\yii1tech\async\cmd\ArrayCommandRunner`.
It does not execute given command, instead it stashes them into the internal array, from which you can access and check them.
Application configuration example:

```php
<?php

return [
    'components' => [
        \yii1tech\async\cmd\CommandDispatcher::class => [
            'class' => \yii1tech\async\cmd\CommandDispatcher::class,
            'commandRunner' => [
                'class' => \yii1tech\async\cmd\ArrayCommandRunner::class,
            ],
        ],
        // ...
    ],
    // ...
];
```

Unit test example:

```php
<?php

use yii1tech\async\cmd\Command;
use yii1tech\async\cmd\CommandDispatcher;

class StatsGenerateLauncherTest extends TestCase
{
    public function testLaunchStatsGenerate(): void
    {
        $launcher = Yii::app()->getComponent(StatsGenerateLauncher::class);
        $launcher->launch(); // dispatches async command inside
        
        $dispatcher = Yii::app()->getComponent(CommandDispatcher::class);
        $runner = $dispatcher->getCommandRunner();
        $command = $runner->getLastCommand();
        
        // check if the async command has been dispatche with the correct parameters:
        $this->assertTrue($command instanceof Command);
        $this->assertSame(StatsCommand::class, $command->getCommandClass());
        $this->assertSame('generate', $command->getCommandAction());
    }
}
```
