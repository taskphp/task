<?php

namespace Task\Plugin;

class SassPlugin implements PluginInterface
{
    protected $options;

    public function __construct(array $options = [], ProcessPlugin $proc =null)
    {
        $this->options = $options;
        $this->proc = $proc ?: new ProcessPlugin;
    }

    public function checkExecutable($bin)
    {
        return realpath($bin);
    }

    public function run(OutputInterface $output = null)
    {
        if (false === $bin = $this->checkExecutable($this->options['bin'])) {
            throw new \InvalidArgumentException('No bin');
        }

        return $this->proc->run($bin, $this->options['arguments'], $this->options['cwd'], $this->options['env'], $output);
    }

    public function read()
    {
        return $this->run()->getOutput();
    }

    public function pipe(WritableInterface $to)
    {
        return $to->write($this->read());
    }
}
