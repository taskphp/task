<?php

namespace Phake;

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

    public function run(PluginContainer $plugins) {
        return call_user_func($this->getWork(), $plugins);
    }

    public function __toString() {
        return $this->getName();
    }
}
