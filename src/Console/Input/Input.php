<?php

namespace Task\Console\Input;

use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Input\InputOption;
use Task\Project;

class Input extends ArgvInput
{
    protected $properties = [];

    public function __construct(Project $project, $argv = null)
    {
        $definition = $project->getDefinition();

        $definition->addOptions([
            new InputOption(
                'property',
                'p',
                InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY
            )
        ]);

        parent::__construct($argv, $definition);
    }

    public function parse()
    {
        parent::parse();

        foreach ($this->getOption('property') as $property) {
            list($key, $value) = explode('=', $property);
            $this->setProperty($key, $value);
        }
    }

    public function getProperty($name, $default = null)
    {
        if (array_key_exists($name, $this->properties)) {
            return $this->properties[$name];
        } elseif ($default !== null) {
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

    public function getDefinition()
    {
        return $this->definition;
    }
}
