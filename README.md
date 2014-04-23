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

Add to your `composer.json`:

```json
{
    "require": {
        "task/task": "~0.1"
    }
}
```

This will allow you to instantiate a `Task\Project`. To run tasks from the command line, install the CLI package globally:

Usage:
  [options] command [arguments]

Options:
  --help           -h Display this help message.
  --quiet          -q Do not output any messa
```bash
$> composer global require task/cli ~0.2
```

If you haven't installed anything this way before you'll need to update your `PATH`:

```bash
export PATH=$PATH:$HOME/.composer/vendor/bin
```

Usage
=====

The only requirements are that you implement a `Taskfile` that returns a `Task\Project`:

```php
<?php

# Include that task/task library and your dependencies.
require 'vendor/autoload.php';

# Instantiate a project by giving it a name.
$project = new Task\Project('foo');

# Return the project!
return $project;
```

The CLI package will look for a `Taskfile` in the current working directory, so you should now be able to:

```bash
$> task
foo version 
ge.
  --verbose        -v|vv|vvv Increase the verbosity of messages: 1 for normal output, 2 for more verbose output and 3 for debug
  --version        -V Display this application version.
  --ansi              Force ANSI output.
  --no-ansi           Disable ANSI output.
  --no-interaction -n Do not ask any interactive question.

Available commands:
  help    Displays help for a command
  list    Lists commands
  shell
```

If you've used Symfony's Console component before this will look familiar! Your `Task\Project` is a `Symfony\Component\Console\Application` and so you have a pretty CLI application out of the box.

Add a task:

```php
<?php

# Include that task/task library and your dependencies.
require 'vendor/autoload.php';

# Instantiate a project by giving it a name.
$project = new Task\Project('foo');

$project->addTask('greet', function () {
    $this->getOutput()->writeln('Hello, World!');
});

# Return the project!
return $project;
```

Now run the task:

```bash
$> task greet
Hello, World!
```

Tasks
=====
