<?php

namespace Phake;

class Project {
    protected $name;
    protected $tasks;
    protected $dependencies;
    protected $plugins;

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

    public function getPlugins() {
        return $this->plugins;
    }

    public function setProperties(PropertyContainer $properties) {
        $this->properties = $properties;
        return $this;
    }

    public function addTask($name, \Closure $work, array $dependencies = []) {
        $this->setTask($name, new Task($name, $work));
        $this->setTaskDependencies($name, $dependencies);
    }

    public function setTask($name, Task $task) {
        $this->tasks[$name] = $task;
    }

    public function getTask($name) {
        return array_key_exists($name, $this->tasks) ? $this->tasks[$name] : null;
    } 

    public function setTaskDependencies($taskName, array $dependencies) {
        $this->dependencies[$taskName] = $dependencies;
    }

    public function getTaskDependencies($taskName) {
        return array_key_exists($taskName, $this->dependencies) ? $this->dependencies[$taskName] : [];
    }

    public function addGroup($name, array $tasks) {
        $project = $this;
        $this->addTask($name, new Task($name, function() use ($project, $tasks) {
            return $project->run($tasks);
        }));
    }

    public function resolveDependencies(array $tasks) {
        $run = [];

        foreach (array_reverse($tasks) as $taskName) {
            array_unshift($run, $taskName);
            foreach (array_reverse($this->getTaskDependencies($taskName)) as $dependency) {
                array_unshift($run, $dependency);
            }
        }

        return array_unique($run);
    }

    public function run(array $tasks) {
        foreach ($this->resolveDependencies($tasks) as $taskName) {
            if ($task = $this->getTask($taskName)) {
                $task->run($this->getPlugins());
            };
        }
    }
}
