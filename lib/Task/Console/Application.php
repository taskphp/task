<?php

namespace Task\Console;

use Symfony\Component\Console;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Application extends Console\Application
{
    public function getDefaultInputDefinition() {
        $definition = parent::getDefaultInputDefinition();
        $definition->addOption(new InputOption('--tasks', '-t', InputOption::VALUE_REQUIRED));

        return $definition;
    }

    public function doRun(InputInterface $input, OutputInterface $output) {
        $project = require($input->getParameterOption(['--tasks', '-t']) ?: 'tasks.php');
        $this->addCommands($project->getTasks());
        
        if (true === $input->hasParameterOption(array('--version', '-V'))) {
            $output->writeln($this->getLongVersion());

            return 0;
        }

        $name = $this->getCommandName($input);
        if (true === $input->hasParameterOption(array('--help', '-h'))) {
            if (!$name) {
                $name = 'help';
                $input = new ArrayInput(array('command' => 'help'));
            } else {
                $this->wantHelps = true;
            }
        }

        if (!$name) {
            $name = 'list';
            $input = new ArrayInput(array('command' => 'list'));
        }

        $run = $project->resolveDependencies($name);

        foreach ($run as $name) {
            $command = $this->find($name);

            $output->writeln(sprintf('Running [%s]', $command->getName()));

            $this->runningCommand = $command;
            $exitCode = $this->doRunCommand($command, $input, $output);
            $this->runningCommand = null;
        }

        return $exitCode;
    }
}
