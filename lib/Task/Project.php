<?php

namespace Task;

use Symfony\Component\Console\Command\Command;

class Project {
    protected $name;
    protected $commands;
    protected $dependencies;

    public $plugins;
    public $properties;

    public function __construct($name) {
        $this->setName($name);
        $this->setPlugins(new PluginContainer);
        $this->setProperties(new PropertyContainer);
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function setPlugins(PluginContainer $plugins) {
        $this->plugins = $plugins;
        return $this;
    }

    public function addPlugins(\Closure $add) {
        return $add($this->plugins);
    }

    public function setProperties(PropertyContainer $properties) {
        $this->properties = $properties;
        return $this;
    }

    public function includeTasks($path) {
        $work = require "$path.php";
        return $work($this);
    }

    public function addTask(Command $task) {
        $this->tasks[] = $task;
    }

    public function getTasks() {
        return $this->tasks;
    }

    public function add() {
        $name = null;
        $task = null;
        $dependencies = [];

        $args = func_get_args();

        if (is_string($args[0])) {
            $name = $args[0];
            if (isset($args[1])) {
                if ($args[1] instanceof \Closure) {
                    $work = $args[1];
                } else {
                    throw new Exception("Work must be Closure");
                }
            } else {
                throw new Exception("Missing work");
            }

            if (isset($args[2])) {
                $dependencies = $args[2];
            }

            $task = new Command($name);
            $task->setCode($work);
        } else {
            $work = $args[0];
            if ($work instanceof Command) {
                $name = $work->getName();
                $task = $work;
            }

            if (isset($args[1])) {
                $dependencies = $args[1];
            }
        }

        $this->addTask($task);
        $this->setTaskDependencies($name, $dependencies);
    }

    public function setTaskDependencies($taskName, array $dependencies) {
        $this->dependencies[$taskName] = $dependencies;
    }

    public function getTaskDependencies($taskName) {
        return array_key_exists($taskName, $this->dependencies) ? $this->dependencies[$taskName] : [];
    }

    public function resolveDependencies($taskName) {
        $run = [$taskName];

        foreach ($this->getTaskDependencies($taskName) as $dependency) {
            $run[] = $dependency;
            $run = array_merge($run, $this->getTaskDependencies($dependency));
        }

        return array_reverse(array_unique($run));
    }
}
