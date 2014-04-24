<?php

namespace Task;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\OutputInterface;
use Task\Plugin\Console\Output\Output;
use Symfony\Component\Console\Command\Command as BaseCommand;
use Task\Console\Command\ShellCommand;
use Task\Console\Command\Command;
use Task\Injector;

class Project extends Application
{
    protected $container;
    protected $dependencies;

    public function __construct($name, $version = null)
    {
        parent::__construct($name, $version);
        $this->setAutoExit(false);
        $this->setInjector(new Injector($this->getContainer()));
    }

    public function setInjector(Injector $injector)
    {
        $this->injector = $injector;
    }

    public function getContainer()
    {
        if (!$this->container) {
            $this->container = new \Pimple;
        }

        return $this->container;
    }

    public function getDefaultCommands()
    {
        $commands = parent::getDefaultCommands();
        $commands[] = new ShellCommand;

        return $commands;
    }

    public function run(InputInterface $input = null, OutputInterface $output = null)
    {
        return parent::run($input, $output ?: new Output);
    }

    public function runTask($name, OutputInterface $output = null)
    {
        $input = new ArrayInput(['command' => $name]);
        $output = $output ?: new Output;
        return $this->doRun($input, $output);
    }

    protected function doRunCommand(BaseCommand $command, InputInterface $input, OutputInterface $output)
    {
        if (!($command instanceof Command)) {
            return parent::doRunCommand($command, $input, $output);
        }

        $run = array_merge(
            $this->resolveDependencies($command),
            [$command]
        );

        foreach ($run as $command) {
            $exitCode = parent::doRunCommand($command, $input, $output);
        }

        return $exitCode;
    }


    public function inject(\Closure $inject)
    {
        return $inject($this->getContainer());
    }

    public function extend($path)
    {
        $extend = require $path;
        return $extend($this);
    }

    public function parseArguments($args)
    {
        if (count($args) < 2) {
            throw new \InvalidArgumentException("Must provide a name and task");
        }

        $name = array_shift($args);
        $work = array_pop($args);
        $description = null;
        $dependencies = [];

        if (!empty($args)) {
            if (count($args) == 2) {
                list($description, $dependencies) = $args;
            } elseif (is_string($args[0])) {
                $description = array_shift($args);
            } else {
                $dependencies = array_shift($args);
            }
        }

        return [$name, $description, $dependencies, $work];
    }

    /**
     * addTask($name, $work);
     * addTask($name, $description, $work);
     * addTask($name, $dependencies, $work);
     * addTask($name, $description, $dependencies, $work);
     */
    public function addTask()
    {
        $args = func_get_args();
        list($name, $description, $dependencies, $work) = $this->parseArguments($args);

        # Existing command
        if ($work instanceof BaseCommand) {
            return parent::add($work);
        }

        $task = new Command($name);
        $task->setDescription($description);

        switch (true) {
            # Basic closure
            #
            case $work instanceof \Closure:
                $work = $work->bindTo($task);
                $task->setCode($work);
                break;

            case is_array($work):
                if (is_callable(end($work))) {
                    # Injector
                    #
                    reset($work);
                    $injector = $this->injector;
                    $task->setCode($injector($work, $task));
                } else {
                    # Group
                    #
                    $task->setCode(function () {
                    });
                    $dependencies = array_merge($work, $dependencies);
                }

                break;
            default:
                throw new \InvalidArgumentException("Unrecognised task signature for $name");
                break;
        }

        parent::add($task);
        $this->setTaskDependencies($name, $dependencies);

        return $task;
    }

    public function setTaskDependencies($taskName, array $dependencies)
    {
        $this->dependencies[$taskName] = $dependencies;
    }

    public function getTaskDependencies($taskName)
    {
        return array_key_exists($taskName, $this->dependencies) ? $this->dependencies[$taskName] : [];
    }

    public function resolveDependencies(Command $task, $nested = false)
    {
        $run = [];
        $taskName = $task->getName();

        $dependencies = $this->getTaskDependencies($taskName);
        foreach (array_reverse($dependencies) as $depName) {
            $dependency = $this->find($depName);
            $run[] = $dependency;
            $run = array_merge(
                $run,
                $this->resolveDependencies($dependency, true)
            );
        }

        return $nested ? $run : array_reverse(array_unique($run, SORT_REGULAR));
    }
}
