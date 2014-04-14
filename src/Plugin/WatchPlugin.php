<?php

namespace Task\Plugin;

use mbfisher\Watch\InotifyWatcher;
use mbfisher\Watch\IteratorWatcher;

class WatchPlugin implements PluginInterface
{
    public function init($path, $pattern = null)
    {
        if (function_exists('inotify_init')) {
            return new InotifyWatcher($path, $pattern);
        } else {
            return new IteratorWatcher($path, $pattern);
        }
    }
}
