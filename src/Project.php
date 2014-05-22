<?php

namespace Task;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Task\Plugin\Console\Output\Output;
use Symfony\Component\Console\Command\Command as BaseCommand;
use Task\Console\Command\ShellCommand;
use Task\Console\Command\Command;
use Task\Console\Command\GroupCommand;
use Task\Injector;

class Project extends Application
{
    protected $container;
    protected $dependencies;
    protected $injector;

    /**
     * @param string $name
     */
    public function __construct($name, $version = null)
    {
        parent::__construct($name, $version);
        $this->setAutoExit(false);
        $this->setInjector(new Injector($this->getContainer()));
    }

    public function getDefaultInputDefinition()
    {
        $definition = parent::getDefaultInputDefinition();

        $definition->addOption(
            new InputOption(
                'property',
                'p',
                InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY
            )
        );

        return $definition;
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

    /**
     * @param string $name
     */
    public function runTask($name, InputInterface $input, OutputInterface $output)
    {
        return $this->doRunCommand($this->get($name), $input, $output);
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

        $exitCode = 0;
        foreach ($run as $command) {
            $output->writeln("Running {$command->getName()}...");
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

        if (!is_callable($extend)) {
            throw new \InvalidArgumentException("Extension script should return callable!");
        }

        return call_user_func($extend, $this);
    }

    public function parseArguments(array $args)
    {
        if (empty($args)) {
            throw new \InvalidArgumentException("Must provide a task!");
        }

        $name = null;
        $description = null;
        $dependencies = [];

        $work = array_pop($args);

        if (!empty($args)) {
            $name = array_shift($args);
        } elseif (!($work instanceof BaseCommand)) {
            throw new \InvalidArgumentException("Work must be a Command instance");
        }

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
     * addTask($work);
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
        #
        if ($work instanceof BaseCommand) {
            if ($name) {
                $work->setName($name);
            }
            $task = $work;
        } else {
            $task = new Command($name);

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
                        $task = new GroupCommand($name, $this, $work);
                    }

                    break;
                default:
                    throw new \InvalidArgumentException("Unrecognised task signature for $name");
            }
        }

        if ($description) {
            $task->setDescription($description);
        }

        parent::add($task);
        $this->setTaskDependencies($name, $dependencies);

        return $task;
    }

    public function setTaskDependencies($taskName, array $dependencies)
    {
        $this->dependencies[$taskName] = $dependencies;
    }

    /**
     * @param string $taskName
     */
    public function getTaskDependencies($taskName)
    {
        return array_key_exists($taskName, $this->dependencies) ? $this->dependencies[$taskName] : [];
    }

    public function resolveDependencies(BaseCommand $task, $nested = false)
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

        return $nested ? $run : array_unique(array_reverse($run), SORT_REGULAR);
    }
}
