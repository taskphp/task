<?php

namespace Task\Plugin;

use Task\Plugin\PluginInterface;

class ArchivePlugin implements PluginInterface {
    public function getBuilder($path = null) {
        return new Archive\ArchiveBuilder($path);
    }
}
