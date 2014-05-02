<?php

namespace Task\Console\Command;

use Symfony\Component\Console\Command\Command as BaseCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\OutputInterface;

class Command extends BaseCommand
{
    protected $properties = [];
    protected $input;
    protected $output;

    public function configure()
    {
        $this
            ->addOption(
                'property',
                'p',
                InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY
            );
    }

    public function run(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = $output;
        return parent::run($input, $output);
    }

    public function getInput()
    {
        return $this->input;
    }

    public function getOutput()
    {
        return $this->output;
    }

    public function getProperty($name, $default = null, InputInterface $input = null)
    {
        if (array_key_exists($name, $this->properties)) {
            return $this->properties[$name];
        }

        $input = $input ?: $this->getInput();

        foreach ($input->getOption('property') as $property) {
            list($key, $value) = explode('=', $property);
            if ($key == $name) {
                $this->setProperty($name, $value);
                return $value;
            }
        }

        if ($default !== null) {
            return $default;
        } else {
            throw new \InvalidArgumentException("Unknown property $name");
        }
    }

    public function setProperty($name, $value)
    {
        $this->properties[$name] = $value;
        return $this;
    }

    public function runTask($name, OutputInterface $output = null, InputInterface $input = null)
    {
        $input = $input ?: new ArrayInput(['command' => $name]);
        $output = $output ?: new Output;
        return $this->getApplication()->get($name)->run($input, $output);
    }

}
