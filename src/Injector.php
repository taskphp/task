<?php

namespace Task;

class Injector
{
    protected $container;

    public function __construct(\Pimple $container)
    {
        $this->container = $container;
    }

    public function __invoke(array $arguments, $bindTo = null)
    {
        $callback = array_pop($arguments);

        if (!is_callable($callback)) {
            throw new \InvalidArgumentException("Last element must be callable");
        }

        if ($callback instanceof \Closure) {
            $callback = $callback->bindTo($bindTo);
        }

        # Can't do this with array_map because exceptions are swallowed (see
        # https://bugs.php.net/bug.php?id=55416).
        $args = [];
        foreach ($arguments as $id) {
            $args[] = $this->container[$id];
        }

        return function () use ($callback, $args) {
            return call_user_func_array($callback, $args);
        };
    }
}
