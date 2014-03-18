<?php

namespace Task;

class PluginContainer extends \Pimple
{
    public function __invoke(callable $inject)
    {
        return $inject($this);
    }
}
