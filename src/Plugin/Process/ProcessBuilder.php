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

    public function run()
    {
        return $this->getProcess()->run();
    }

    public function read()
    {
        return $this->run()->getOutput();
    }

    public function write($data)
    {
        return $this->setInput($data)->getProcess();
    }

    public function pipe(WritableInterface $to)
    {
        return $to->write($this->read());
    }
}
