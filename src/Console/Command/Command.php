<?php

namespace Task\Console\Command;

use Symfony\Component\Console\Command\Command as BaseCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Task\Project;
use Task\Plugin\Stream\WritableInterface;
use Task\Plugin\Console\Output\ProxyOutput;

class Command extends BaseCommand
{
    protected $properties = [];
    protected $hasProperties = false;
    protected $input;
    protected $output;

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->parseProperties($input);
        $this->setIO($input, $output);
    }

    public function parseProperties(InputInterface $input)
    {
        if ($input->hasOption('property')) {
            $this->hasProperties = true;

            foreach ($input->getOption('property') as $property) {
                list($key, $value) = explode('=', $property);
                $this->properties[$key] = $value;
            }
        }
    }

    public function setInput(InputInterface $input)
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
        if (!($output instanceof WritableInterface)) {
            $output = (new ProxyOutput)->setTarget($output);
        }

        $this->output = $output;
        return $this;
    }

    public function getOutput()
    {
        return $this->output;
    }

    public function setIO(InputInterface $input, OutputInterface $output)
    {
        $this->setInput($input);
        $this->setOutput($output);
        return $this;
    }

    public function getProperty($name, $default = null)
    {
        if (!$this->hasProperties) {
            throw new \RuntimeException("No properties could be found");
        }

        if (array_key_exists($name, $this->properties)) {
            return $this->properties[$name];
        } elseif ($default !== null) {
            return $default;
        } else {
            throw new \InvalidArgumentException("Unknown property $name");
        }
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
