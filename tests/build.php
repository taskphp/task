<?php

$project = new Task\Project('test');

$project->addTask('greet', function() {
    echo "Hello, World!\n";
});

/*$project->addTask('welcome', function() {
    echo "Welcome to Task.\n";
}, ['greet']);

$project->addTask('ls', function($plugins) {
    $plugins['ps']->run('ls');
});

$project->addTask('touch', function($plugins) {
    $plugins['fs']->touch('./foo');
});*/

return $project;
