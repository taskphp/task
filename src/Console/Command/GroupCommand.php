<?php

namespace Task\Console\Command;

use Task\Project;
use Symfony\Component\Console\Command\Command;

class GroupCommand extends Command
{
    public function __construct($name, array $tasks, Project $project)
    {
        parent::__construct($name);
        $this->tasks = $tasks;
        $this->project = $project;
    }
}
