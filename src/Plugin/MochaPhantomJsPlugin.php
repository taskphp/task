<?php

namespace Task\Plugin;

class MochaPhantomJsPlugin implements PluginInterface
{
    public function __construct($prefix = 'mocha-phantomjs', $cwd = '.')
    {
        $this->prefix = $prefix;
        $this->cwd = $cwd;
    }

    public function getCommand()
    {
        return new Mocha\PhantomJsCommand($this->prefix, $this->cwd);
    }
}
