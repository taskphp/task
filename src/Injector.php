<?php

namespace Task;

class Injector
{
    public function __construct(\Pimple $container)
    {
        $this->container = $container;
    }

    public function __invoke(array $arguments, $bindTo = null)
    {

        $callback = array_pop($arguments);

        if (!is_callable($callback)) {
            throw new \InvalidArgumentExceptoin("Last element must be callable");
        }

        $callback = $callback->bindTo($bindTo);
        
        $container = $this->container;
        $args = array_map(function ($id) use ($container) {
            return $container[$id];
        }, $arguments);

        return function () use ($callback, $args) {
            return call_user_func_array($callback, $args);
        };
    }
}
