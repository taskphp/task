<?php

namespace Task;

class ProjectFinder
{
    public function find()
    {
        $taskfile = './Taskfile';
        if (!file_exists($taskfile)) {
            throw new \RuntimeException("$taskfile not found");
        }

        if (filesize($taskfile) === 0) {
            throw new \LogicException("Taskfile is empty");
        }

        $project = require $taskfile;

        if (!($project instanceof Project)) {
            throw new \InvalidArgumentException("Taskfile must return a Project");
        }

        return $project;
    }
}
