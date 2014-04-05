<?php

namespace Task\Console;

use Symfony\Component\Console\Application as BaseApplication;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\ConsoleOutput;
use Task\Project;
use Task\Console\Command\ShellCommand;

class Application extends BaseApplication
{
    public function __construct($name = null, $version = null)
    {
        parent::__construct($name, $version);
        $this->setAutoExit(false);
    }

    public function run(InputInterface $input = null, OutputInterface $output = null)
    {
        $input = $input ?: new ArgvInput;
        $output = $output ?: new ConsoleOutput;

        $commandName = $this->getCommandName($input);

        foreach ($this->getDefaultCommands() as $command) {
            if ($commandName == $command->getName()) {
                return parent::run($input, $output);
            }
        }

        $this->configureIO($input, $output);

        $taskfile = $this->getTaskfile($input);
        $project = $this->getProject($taskfile);
        $input = new ArrayInput(['command' => $commandName]);
        return $project->run($input, $output);
    }

    public function getTaskfile(InputInterface $input)
    {
        $taskfile = $input->getParameterOption(['-t', 'taskfile']) ?: './Taskfile';

        if (($realTaskfile = realpath($taskfile)) === false) {
            throw new Exception("$taskfile not found");
        }

        // Need to test whether the Taskfile is empty
        if (filesize($realTaskfile) === 0) {
            throw new Exception("Taskfile is empty");
        }

        return $realTaskfile;
    }

    public function getProject($taskfile)
    {
        $project = require $taskfile;

        if (!($project instanceof Project)) {
            throw new \InvalidArgumentException("Taskfile must return a Project");
        }

        return $project;
    }

    public function getDefaultInputDefinition()
    {
        $definition = parent::getDefaultInputDefinition();
        $definition->addOption(new InputOption('taskfile', 't', InputOption::VALUE_REQUIRED));
        return $definition;
    }

    public function getDefaultCommands()
    {
        return array_merge(parent::getDefaultCommands(), [
            new ShellCommand
        ]);
    }
}
