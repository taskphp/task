<?php

$project = new Phake\Project('test');

$project->addTask('greet', function() {
    echo "Hello, World!\n";
});

$project->addTask('welcome', function() {
    echo "Welcome to Phake.\n";
}, ['greet']);

$project->addTask('ls', function($plugins) {
    $plugins['ps']->run('ls');
});

return $project;
