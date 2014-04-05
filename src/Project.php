<?php

namespace Task;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Command\Command;
use Task\Console\Command\ShellCommand;
use Task\Injector;

class Project extends Application
{
    protected $container;
    protected $dependencies;

    public function __construct($name)
    {
        parent::__construct($name);
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

    public function inject(\Closure $inject)
    {
        return $inject($this->getContainer());
    }

    public function extend($path)
    {
        $extend = require "$path.php";
        return $extend($this);
    }

    public function getDefaultCommands()
    {
        $commands = parent::getDefaultCommands();
        $commands[] = new ShellCommand;

        return $commands;
    }

    public function runTask($name, OutputInterface $output = null)
    {
        $input = new ArrayInput(['command' => $name]);
        return $this->run($input, $output);
    }

    public function doRun(InputInterface $input, OutputInterface $output)
    {
        if ($input->hasParameterOption(array('--version', '-V')) === true) {
            $output->writeln($this->getLongVersion());

            return 0;
        }

        $name = $this->getCommandName($input);

        if ($input->hasParameterOption(array('--help', '-h')) === true) {
            if (!$name) {
                return $this->doRunCommand(
                    $this->get('help'),
                    new ArrayInput(array('command' => 'help')),
                    $output
                );
            } else {
                $this->wantHelps = true;
            }
        }

        if (!$name) {
            $name = 'list';
            return $this->doRunCommand(
                $this->get('list'),
                new ArrayInput(array('command' => 'list')),
                $output
            );
        }

        $run = array_merge(
            $this->resolveDependencies($name),
            [$name]
        );

        foreach ($run as $name) {
            $command = $this->find($name);

            $this->runningCommand = $command;
            $exitCode = $this->doRunCommand($command, $input, $output);
            $this->runningCommand = null;
        }

        return $exitCode;
    }

    public function addTask($name, $work, array $dependencies = [])
    {
        # Existing command
        if ($work instanceof Command) {
            return parent::add($work);
        }

        $task = new Command($name);

        switch (true) {
            # Basic closure
            case $work instanceof Closure:
                $work = function ($input, $output) use ($work) {
                    return $work($output);
                };
                $task->setCode($work);
                break;

            case is_array($work):
                if (is_callable(end($work))) {
                    # Injector
                    reset($work);
                    $injector = $this->injector;
                    $task->setCode(function ($input, $output) use ($injector, $work) {
                        return $injector($work, [$output]);
                    });
                } else {
                    # Group
                    $task->setCode(function () {
                    });
                    $dependencies = array_merge($work, $dependencies);
                }

                break;
        }

        parent::add($task);
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
