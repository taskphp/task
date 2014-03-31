<?php

namespace Task\Plugin\Console;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\OutputInterface;

class CommandRunner
{
    protected $parameters = [];

    public function __construct(Application $app, $commandName)
    {
        if ($command = $this->findCommand($app, $commandName)) {
            $command->setApplication($app);
            $command->mergeApplicationDefinition();

            $this->command = $command->getName();
            $this->definition = $command->getDefinition();
            $this->app = $app;
        } else {
            throw new \InvalidArgumentException("No command found for [$commandName]");
        }
    }

    public function findCommand(Application $app, $commandName)
    {
        return $app->get($commandName);
    }

    public function run(OutputInterface $output)
    {
        $input = new ArrayInput(array_merge([
            'command' => $this->command
        ], $this->parameters));
        return $this->app->run($input, $output);
    }

    public function __call($method, array $arguments = [])
    {
        if (strpos($method, 'set') !== 0) {
            throw new \InvalidArgumentException("Unknown method $method");
        }

        $alias = $this->parseMethodName(substr($method, 3));
        $value = $arguments[0];

        if ($this->definition->hasOption($alias)) {
            $this->parameters["--$alias"] = $value;
        } elseif ($this->definition->hasArgument($alias)) {
            $this->parameters[$alias] = $value;
        } else {
            throw new \InvalidArgumentException("Unrecognised parameter $alias");
        }

        return $this;
    }

    public function parseMethodName($name)
    {
        $parts = preg_split('/(?<=[a-z])(?![a-z])/', $name, -1, PREG_SPLIT_NO_EMPTY);
        return implode('-', array_map('strtolower', $parts));
    }

    public function getParameters()
    {
        return $this->parameters;
    }
}
