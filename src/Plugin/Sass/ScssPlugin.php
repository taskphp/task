<?php

namespace Task\Plugin\Sass;

class ScssPlugin extends SassPlugin
{
    public function __construct(array $arguments = [])
    {
        parent::__construct(array_merge(['--scss'], $arguments));
    }
}
