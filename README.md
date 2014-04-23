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

$project->addTask('welcome', [function ($output) {
    $output->writeln('Hello!');
}]);

$project->addTask('test', ['phpspec', function ($output, $phpspec) {
    return $phpspec->command('run')
        ->setFormat('pretty')
        ->setVerbose(true)
        ->run($output);
}]);

$project->addTask('css', ['fs', 'sass', function ($output, $fs, $sass) {
    $css = $fs->open('/tmp/my.scss')
        ->pipe($sass)
        ->pipe($fs->touch('/tmp/my.css'));

    $output->write($css->read());
}]);

$project->addTask('css.watch', ['watch', function ($output, $watch) use ($project) {
    $watch->init('/tmp/my.scss')
        ->addListener('modify', function ($event) use ($project, $output) {
            $project->runTask('css', $output);
        })
        ->start();
}]);

return $project;
```
