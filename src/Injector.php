<?php

namespace Task;

class Injector
{
    private $container;

    public function __construct(\Pimple $container)
    {
        $this->container = $container;
    }

    public function getContainer()
    {
        return $this->container;
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

        $container = $this->getContainer();
        return function () use ($callback, $arguments, $container) {
            # Can't do this with array_map because exceptions are swallowed (see
            # https://bugs.php.net/bug.php?id=55416).
            $args = [];
            foreach ($arguments as $id) {
                $args[] = $container[$id];
            }

            return call_user_func_array($callback, $args);
        };
    }
}
