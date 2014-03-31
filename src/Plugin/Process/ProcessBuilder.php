<?php

namespace Task\Plugin\Process;

use Symfony\Component\Process\ProcessBuilder as BaseProcessBuilder;
use Task\Plugin\Stream\WritableInterface;

class ProcessBuilder extends BaseProcessBuilder implements WritableInterface
{
    public function getProcess()
    {
        return Process::extend(parent::getProcess());
    }

    public function write($data)
    {
        return $this->setStdin($data)->getProcess();
    }
}
