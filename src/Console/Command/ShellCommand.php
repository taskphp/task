<?php

namespace Task\Console\Command;

use Symfony\Component\Console\Shell;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ShellCommand extends Command
{
    public function configure()
    {
        $this->setName('shell');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $app = $this->getApplication();

        $taskfile = $app->getTaskfile($input);
        $project = $app->getProject($taskfile);

        $shell = new Shell($project);
        return $shell->run();
    }
}
