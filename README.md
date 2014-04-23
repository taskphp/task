task
====

[![Build Status](https://travis-ci.org/taskphp/task.svg?branch=master)](https://travis-ci.org/taskphp/task)

A Phing killing PHP task runner.

```php
<?php

use Task\Plugin;

require 'vendor/autoload.php';

$project = new Task\Project('wow');

$project->inject(function ($container) {
    $conatiner['phpspec'] = new Plugin\PhpSpecPlugin;
    $container['fs'] = new Plugin\FilesystemPlugin;
    $container['sass'] = (new Plugin\Sass\ScssPlugin)
        ->setPrefix('sass');
    $container['watch'] = new Plugin\WatchPlugin;

});

$project->addTask('welcome', function () {
    $this->getOutput()->writeln('Hello!');
});

$project->addTask('test', ['phpspec', function ($phpspec) {
    $phpspec->command('run')
        ->setFormat('pretty')
        ->setVerbose(true)
        ->run($this->getOutput());
}]);

$project->addTask('css', ['fs', 'sass', function ($fs, $sass) {
    fs->open('my.scss')
        ->pipe($sass)
        ->pipe($fs->touch('my.css'));
}]);

$project->addTask('css.watch', ['watch', function ($watch) use ($project) {
    $output = $this->getOutput();
    
    $watch->init('my.scss')
        ->addListener('modify', function ($event) use ($project, $output) {
            $project->runTask('css', $output);
        })
        ->start();
}]);

return $project;
```

Installation
============

Composer
--------

```json
{
    "require": {
        "task/task": "~0.1"
    }
}
```
