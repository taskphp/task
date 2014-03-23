<?php

namespace Task\Console;

use Task\Exception;
use Task\Console\Command;
use Symfony\Component\Console\Application as SymfonyApplication;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\OutputInterface;

class Application extends SymfonyApplication
{
    public function __construct()
    {
        parent::__construct('task', 'v1.0.0');
    }

    public function getDefaultInputDefinition()
    {
        $definition = parent::getDefaultInputDefinition();
        $definition->addOption(new InputOption(
            '--taskfile',
            '-t',
            InputOption::VALUE_REQUIRED,
            'Path to Taskfile'
        ));

        return $definition;
    }

    public function getDefaultCommands()
    {
        $commands = parent::getDefaultCommands();
        $commands[] = new Command\ShellCommand;

        return $commands;
    }

    public function setTaskfile($taskfile)
    {
        if (empty($taskfile)) {
            throw new \RuntimeException("Canno set empty taskfile");
        }

        $this->taskfile = $taskfile;
    }

    public function getTaskfileOption(InputInterface $input)
    {
        return $input->getParameterOption(['--tasks', '-t']);
    }

    public function getTaskfile(InputInterface $input = null)
    {
        $taskfile = './Taskfile';

        if (isset($this->taskfile)) {
            $taskfile = $this->taskfile;
        } elseif (isset($input)) {
            if ($option = $this->getTaskfileOption($input)) {
                $taskfile = $option;
            }
        }

        if (false === $realTaskfile = realpath($taskfile)) {
            throw new Exception("Taskfile $taskfile not found");
        }

        return $realTaskfile;
    }


    public function doRun(InputInterface $input, OutputInterface $output)
    {
        if (true === $input->hasParameterOption(array('--version', '-V'))) {
            $output->writeln($this->getLongVersion());

            return 0;
        }

        $project = require $this->getTaskfile($input);
        $this->addCommands($project->getTasks());

        $name = $this->getCommandName($input);

        if (true === $input->hasParameterOption(array('--help', '-h'))) {
            if (!$name) {
                return $this->doRunCommand(
                    $this->get('help'),
                    new ArrayInput(array('command' => 'help')),
                    $output
                );
            } else {
                $this->wantHelps = true;
            }
        }

        if (!$name) {
            $name = 'list';
            return $this->doRunCommand(
                $this->get('list'),
                new ArrayInput(array('command' => 'list')),
                $output
            );
        }

        $run = array_merge(
            $project->resolveDependencies($name),
            [$name]
        );

        foreach ($run as $name) {
            $command = $this->find($name);

            $this->runningCommand = $command;
            $exitCode = $this->doRunCommand($command, $input, $output);
            $this->runningCommand = null;
        }

        return $exitCode;
    }
}
