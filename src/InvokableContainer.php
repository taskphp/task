<?php

namespace Task;

class InvokableContainer extends \Pimple
{
    public function __invoke(callable $inject)
    {
        return $inject($this);
    }
}
