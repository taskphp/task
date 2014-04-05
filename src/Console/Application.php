<?php

namespace Task\Console;

use Symfony\Component\Console\Application as BaseApplication;
use Symfony\Component\Console\Input\InputInterface;

class Application extends BaseApplication
{
    public function getCommandName(InputInterface $input)
    {
        return 'run';
    }

    public function getDefaultCommands()
    {
        $commands = parent::getDefaultCommands();
        $commands[] = new Command\RunCommand;
        return $commands;
    }

    public function getDefinition()
    {
        $definition = parent::getDefinition();
        $definition->setArguments();
        return $definition;
    }
}
