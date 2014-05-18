<?php

namespace Task\Console\Command;

use Symfony\Component\Console\Command\Command as BaseCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Task\Console\Input\Input;
use Task\Project;

class Command extends BaseCommand
{
    protected $properties = [];
    protected $input;
    protected $output;

    public function run(InputInterface $input, OutputInterface $output)
    {
        if (!($input instanceof Input)) {
            throw new \InvalidArgumentException("Input must be instance of Task\Console\Input\Input");
        }

        $this->setInput($input);
        $this->setOutput($output);
        return parent::run($input, $output);
    }

    public function setInput(Input $input)
    {
        $this->input = $input;
        return $this;
    }

    public function getInput()
    {
        return $this->input;
    }

    public function setOutput(OutputInterface $output)
    {
        $this->output = $output;
        return $this;
    }

    public function getOutput()
    {
        return $this->output;
    }

    public function setIO(Input $input, OutputInterface $output)
    {
        $this->setInput($input);
        $this->setOutput($output);
        return $this;
    }

    public function getProperty($name, $default = null)
    {
        return $this->getInput()->getProperty($name, $default);
    }

    public function runTask($name)
    {
        if (($project = $this->getApplication()) instanceof Project) {
            $project->runTask($name, $this->getInput(), $this->getOutput());
        } else {
            throw new \RuntimeException("Can only run tasks on Task\Project instances");
        }
    }
}
