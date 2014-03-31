<?php

namespace Task;

use Closure;
use Symfony\Component\Console\Command\Command;

class Project extends InvokableContainer
{
    protected $name;
    protected $commands;
    protected $dependencies;

    public function __construct($name)
    {
        $this->setName($name);
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function extend($path)
    {
        $extend = require "$path.php";
        return $extend($this);
    }

    public function getTasks()
    {
        if (empty($this->tasks)) {
            throw new Exception("No tasks");
        }
        
        return $this->tasks;
    }

    public function add($name, $work, array $dependencies = [])
    {
        $task = null;

        # Existing command
        if ($work instanceof Command) {
            $task = $work;
        # Basic closure
        } elseif ($work instanceof Closure) {
            $work = function ($input, $output) use ($work) {
                return $work($output);
            };
            $task = new Command($name);
            $task->setCode($work);
        } elseif (is_array($work)) {
            $task = new Command($name);
            # Injector
            if (is_callable($work = end($work)) {
                $injector = $this->injector;
                $task->setCode(function ($input, $output) use ($injector, $work) {
                    return $injector($work, $output);
                });
            # Group
            } else {
                $task->setCode(function () {
                });
                $dependencies = array_merge($work, $dependencies);
            }
        }

        $this->addTask($task);
        $this->setTaskDependencies($name, $dependencies);
    }

    public function setTaskDependencies($taskName, array $dependencies)
    {
        $this->dependencies[$taskName] = $dependencies;
    }

    public function getTaskDependencies($taskName)
    {
        return array_key_exists($taskName, $this->dependencies) ? $this->dependencies[$taskName] : [];
    }

    public function resolveDependencies($taskName, $nested = false)
    {
        $run = [];

        $dependencies = $this->getTaskDependencies($taskName);
        foreach (array_reverse($dependencies) as $dependency) {
            $run[] = $dependency;
            $run = array_merge(
                $run,
                $this->resolveDependencies($dependency, true)
            );
        }

        return $nested ? $run : array_reverse(array_unique($run));
    }
}
