<?php

namespace Task\Plugin;

use mbfisher\Watch\Watcher;
use Symfony\Component\EventDispatcher\EventDispatcher;

class WatchPlugin extends EventDispatcher implements PluginInterface
{
    public function init($path, $pattern = null)
    {
        return new Watcher($path, $pattern);
    }
}
