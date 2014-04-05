<?php

namespace Task\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class RunCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('run')
            ->addArgument('task', InputArgument::REQUIRED)
            ->addOption('taskfile', 't', InputOption::VALUE_REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $taskfile = $input->getOption('taskfile') ?: './Taskfile';

        if (($realTaskfile = realpath($taskfile)) === false) {
            throw new Exception("$taskfile not found");
        }

        // Need to test whether the Taskfile is empty
        if (filesize($realTaskfile) === 0) {
            throw new Exception("Taskfile is empty");
        }

        $project = require $realTaskfile;
        $project->run($input->getArgument('task'), $output);
    }
}
