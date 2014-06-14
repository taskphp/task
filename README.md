task/task
=========

[![Build Status](https://travis-ci.org/taskphp/task.svg?branch=master)](https://travis-ci.org/taskphp/task)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/taskphp/task/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/taskphp/task/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/taskphp/task/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/taskphp/task/?branch=master)
[![Latest Stable Version](https://poser.pugx.org/task/task/v/stable.png)](https://packagist.org/packages/task/task)
[![Total Downloads](https://poser.pugx.org/task/task/downloads.png)](https://packagist.org/packages/task/task)
[![Latest Unstable Version](https://poser.pugx.org/task/task/v/unstable.png)](https://packagist.org/packages/task/task)
[![License](https://poser.pugx.org/task/task/license.png)](https://packagist.org/packages/task/task)

Got a PHP project? Heard of Grunt and Gulp but don't use NodeJS?  Task is a pure PHP task runner.

* Leverage PHP as a scripting language, and as your platform of choice.
* Use loads of nice features inspired by Grunt and Gulp (and Phing).
* Employ Symfony components for effortless CLI goodness.
* Extend with plugins.

For more information and documentation goto [taskphp.github.io](http://taskphp.github.io), or tweet us at [@taskphp](https://twitter.com/taskphp).

Example
=======

```php
<?php

use Task\Plugin;

require 'vendor/autoload.php';

$project = new Task\Project('wow');

$project->inject(function ($container) {
    $container['phpspec'] = new Plugin\PhpSpecPlugin;
    $container['fs'] = new Plugin\FilesystemPlugin;
    $container['sass'] = (new Plugin\Sass\ScssPlugin)
        ->setPrefix('sass');
    $container['watch'] = new Plugin\WatchPlugin;
});

$project->addTask('greet', function () {
    $this->getOutput()->writeln('Hello, World!');
});

$project->addTask('test', ['phpspec', function ($phpspec) {
    $phpspec->command('run')
        ->setFormat('pretty')
        ->setVerbose(true)
        ->pipe($this->getOutput());
}]);

$project->addTask('css', ['fs', 'sass', function ($fs, $sass) {
    fs->open('my.scss')
        ->pipe($sass)
        ->pipe($fs->touch('my.css'));
}]);

$project->addTask('css.watch', ['watch', function ($watch) {
    $watch->init('my.scss')
        ->addListener('modify', function ($event) {
            $this->runTask('css', $this->getOutput());
        })
        ->start();
}]);

return $project;
```

```bash
$> task greet
Hello, World!

$> task test

      Task\Injector

  17  ✔ is initializable
  22  ✔ should call function with services

      Task\Project

  10  ✔ is initializable
  20  ✔ should have a container
  26  ✔ should resolve no dependencies
  32  ✔ should resolve one dependency
  39  ✔ should resolve many dependencies
  50  ✔ should normalize dependencies
  58  ✔ should normalize complex dependencies


2 specs
9 examples (9 passed)
29ms
```
