<?php

namespace Task\Plugin\Sass;

use Task\Plugin\Process\Process as TaskProcess;

class Process extends TaskProcess
{
    protected $tmp;
    protected $output;

    public function run($callback = null)
    {
        $this->tmp = tempnam(sys_get_temp_dir(), 'task');
        $this->setCommandLine($this->getCommandLine()." $this->tmp");
        return parent::run($callback);
    }

    public function getOutput()
    {
        if (!$this->output) {
            $this->output = file_get_contents($this->tmp);
            unlink($this->tmp);
        }

        return $this->output;
    }
}
