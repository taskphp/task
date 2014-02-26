task
====

PHP task runner.

Example
=======

`tasks.php`:

    <?php

    $tasks = new Task\Project('myproject');

    # Add closures as tasks
    $tasks->add('welcome', function ($input, $output) {
        $output->writeln('Welcome to Task!');
    });

    # Add existing Symfony Console commands as tasks
    $tasks->add('mycommand', new MyCommand);

    # Add groups of other tasks as tasks
    $tasks->add('all', ['welcome', 'mycommand']);

    # Tasks can depend on other tasks
    $tasks->add('now', function($input, $output) {
        $output->writeln(date('c'));
    }, ['welcome']);

    # Tasks are Symfony Console commands...
    $tasks->add('greet', function($input, $output) {
        $output->writeln('Hello, '.$input->getArgument('name').'!');
    });

Run it!

    $> task welcome
    Welcome to Task!

    $> task now
    Welcome to Task!
    2014-02-01

    $> task greet Mike
    Hello, Mike!




