<?php

namespace Task\Console\Command;

use Symfony\Component\Console\Command\Command;
use Task\Project;

class GroupCommand extends Command {
    public function __construct($name, array $tasks, Project $project) {
        parent::__construct($name);
        $this->tasks = $tasks;
        $this->project = $project;
    }


