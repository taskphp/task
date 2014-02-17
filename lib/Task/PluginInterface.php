<?php

namespace Task;

interface PluginInterface {
    public static function factory(PluginContainer $plugins);
}
