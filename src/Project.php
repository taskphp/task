<?php

namespace Task;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputDefinition;

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

    public function plugins(\Closure $work) {
        return $work($this->plugins);
    }

    public function setProperties(PropertyContainer $properties) {
        $this->properties = $properties;
        return $this;
    }

    public function properties(\Closure $work) {
        return $work($this->properties);
    }

    public function extend($path) {
        $extend = require "$path.php";
        return $extend($this);
    }

    public function addTask(Command $task) {
        $this->tasks[] = $task;
    }

    public function getTasks() {
        return $this->tasks;
    }

    public function add($name, $work, array $dependencies = [], InputDefinition $definition = null) {
        $task = null;

        if ($work instanceof \Closure) {
            $task = new Command($name);
            $task->setCode($work);
        } elseif ($work instanceof Command) {
            $task = $work;
        } elseif (is_array($work)) {
            $task = new Command($name);
            $task->setCode(function() {});
            $dependencies = array_merge($work, $dependencies);
        }

        if (isset($definition)) {
            $task->setDefinition($definition);
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

    public function resolveDependencies($taskName, $nested = false) {
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
