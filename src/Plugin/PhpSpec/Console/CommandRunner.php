<?php

namespace Task\Plugin\PhpSpec\Console;

use Task\Plugin\Console\CommandRunner as BaseCommandRunner;
use Symfony\Component\Console\Application;

class CommandRunner extends BaseCommandRunner
{
    public function findCommand(Application $app, $commandName)
    {
        $commandClass = $this->getCommandClassName($commandName);
        return new $commandClass;
    }

    public function getCommandClassName($commandName)
    {
        return 'PhpSpec\Console\Command\\'.ucfirst($commandName).'Command';
    }
}
