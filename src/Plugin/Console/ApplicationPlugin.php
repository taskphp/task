<?php

namespace Task\Plugin\Console;

use Task\Plugin\PluginInterface;
use Symfony\Component\Console\Application;

class ApplicationPlugin implements PluginInterface
{
    protected $app;
    protected $commandRunner;

    public function __construct(
        Application $app,
        $commandRunner = 'Task\Plugin\Console\CommandRunner'
    ) {
        $this->app = $app;
        $this->commandRunner = $commandRunner;
    }

    public function command($commandName)
    {
        return new $this->commandRunner($this->app, $commandName);
    }
}
