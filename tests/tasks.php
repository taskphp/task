<?php

use Task\Plugin;

$tasks = new Task\Project('test');

$tasks->addPlugins(function($plugins) {
    $plugins['ps'] = Plugin\ProcessPlugin::factory($plugins);
});

$tasks->add('welcome', function($input, $output) {
    $output->writeln('Welcome to Tasks!');
});

$tasks->add('whoami', function($input, $output) use ($tasks) {
    $whoami = $tasks->plugins['ps']->run('whoami')->getOutput();
    $output->write($whoami);
}, ['welcome']);

return $tasks;
