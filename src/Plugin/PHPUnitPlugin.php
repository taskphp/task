<?php

namespace Task\Plugin;

use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;

class PHPUnitPlugin implements PluginInterface
{
    public function getCommand()
    {
        return new PHPUnit\Command;
    }
}
