<?php

namespace Task\Console;

use Symfony\Component\Console;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Task\Plugin;

class Command extends Console\Command\Command {
    protected function configure() {
        $this
            ->setName('phake')
            ->addArgument('task', InputArgument::REQUIRED)
            ->addOption('project', 'p', InputOption::VALUE_REQUIRED);
    }

    protected  function execute(InputInterface $input, OutputInterface $output) {
        $project = require $input->getOption('project') ?: './build.php';

        $project->addPlugins(function($plugins) {
            $plugins['ps'] = Plugin\ProcessPlugin::factory($plugins);
            #$plugins['fs'] = Plugin\FilesystemPlugin::factory();
        });

        $project->run([$input->getArgument('task')]);
    }
}
