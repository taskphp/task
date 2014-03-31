<?php

namespace Task\Plugin;

use Task\Plugin\Console\ApplicationPlugin;
use PhpSpec\Console\Application;

class PhpSpecPlugin extends ApplicationPlugin implements PluginInterface
{
    public function __construct($version = null)
    {
        parent::__construct(
            new Application($version),
            'Task\Plugin\PhpSpec\Console\CommandRunner'
        );
    }
}
