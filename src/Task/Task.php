<?php

namespace Task;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Task {
    public function __construct($name, \Closure $work) {
        $this->setName($name)->setWork($work);
    }

    public function setName($name) {
        $this->name = $name;
        return $this;
    }

    public function getName() {
        return $this->name;
    }

    public function setWork(\Closure $work) {
        $this->work = $work;
        return $this;
    }

    public function getWork() {
        return $this->work;
    }

    public function run(
        PluginContainer $plugins,
        PropertyContainer $properties,
        InputInterface $input,
        OutputInterface $output
    ) {
        return call_user_func($this->getWork(), $plugins, $properties, $input, $output);
    }

    public function __toString() {
        return $this->getName();
    }
}
