<?php

namespace Task;

class Injector
{
    public function __construct(\Pimple $container)
    {
        $this->container = $container;
    }

    public function __invoke(array $arguments, array $extra = [])
    {

        $callback = array_pop($arguments);

        if (!is_callable($callback)) {
            throw new \InvalidArgumentExceptoin("Last element must be callable");
        }
        
        $args = $extra;
        foreach ($arguments as $id) {
            $args[] = $this->container[$id];
        }

        return call_user_func_array($callback, $args);
    }
}
