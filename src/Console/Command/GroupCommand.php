<?php

namespace Task\Console\Command;

use Task\Project;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Task\Console\Command\Command;

class GroupCommand extends Command
{
    protected $project;
    protected $tasks;

    public function __construct($name, Project $project, array $tasks)
    {
        parent::__construct($name);
        $this->project = $project;
        $this->tasks = $tasks;
    }

    public function getProject()
    {
        return $this->project;
    }

    public function getTasks()
    {
        return $this->tasks;
    }

    public function getCommands()
    {
        $project = $this->getProject();

        $commands = [];
        foreach ($this->getTasks() as $taskName) {
            $task = $project->get($taskName);
            if ($task instanceof Command) {
                $commands = array_values(array_unique(
                    array_merge(
                        $commands,
                        $project->resolveDependencies($task),
                        [$task]
                    ),
                    SORT_REGULAR
                ));
            }
        }

        return $commands;
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $exitCode = 0;
        foreach ($this->getCommands() as $task) {
            $output->writeln("Running {$task->getName()}...");
            $task->run($input, $output);
        }

        return $exitCode;
    }
}
