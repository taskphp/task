<?php

namespace Task\Plugin\Process;

class AbstractCommand extends ProcessBuilder
{
    public function addArgument($arg)
    {
        $this->args[] = $arg;

        return $this;
    }

    public function addOption($option, $value = null)
    {
        $this->options[] = $option;
        if ($value !== null) {
            $this->options[] = $value;
        }

        return $this;
    }

    public function getProcess()
    {
        $this->setArguments(array_merge($this->options, $this->args));
        return parent::getProcess();
    }
}
