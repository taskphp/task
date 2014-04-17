<?php

namespace Task\Plugin;

class MochaPlugin implements PluginInterface
{
    public function getCommand()
    {
        return new Mocha\Command;
    }
}
