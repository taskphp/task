<?php

namespace Task\Plugin\Sass;

use Task\Plugin\Process\ProcessBuilder;
use Task\Plugin\PluginInterface;
use Task\Plugin\Stream;

class SassPlugin extends ProcessBuilder implements PluginInterface, Stream\ReadableInterface, Stream\WritableInterface
{
    public function __construct(array $arguments = [])
    {
        parent::__construct(array_merge(['-s'], $arguments));
    }

    public function getProcess()
    {
        return Process::extend(parent::getProcess());
    }
}
