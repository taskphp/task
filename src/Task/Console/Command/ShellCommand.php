<?php

namespace Task\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Shell;

class ShellCommand extends Command {
    public function configure() {
        $this->setName('shell');
    }

    public function execute(InputInterface $input, OutputInterface $output) {
        $app = $this->getApplication();
        if ($taskfile = $app->getTaskfileOption($input)) {
            $app->setTaskfile($taskfile);
        }

        $shell = new Shell($app);
        return $shell->run();
    }
}
