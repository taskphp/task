<?php

namespace Task\Plugin\Process;

use Symfony\Component\Process\ProcessBuilder;

abstract class AbstractCommandPlugin
{
    protected $prefix;
    protected $options = [
        'args' => [],
        'cwd' => '.',
        'env' => []
    ];

    public function __construct($prefix, array $options = [])
    {
        $this->setPrefix($prefix);
        $this->extendOptions($options);
    }

    public function setPrefix($prefix)
    {
        $this->prefix = $prefix;

        return $this;
    }

    public function getPrefix()
    {
        return $this->prefix;
    }

    public function extendOptions(array $options)
    {
        $this->options = array_merge($this->options, $options);
        return $this;
    }

    public function build($command = null, array $args = [])
    {
        return ProcessBuilder::create($command ? [$command] : null)
            ->setPrefix($this->getPrefix())
            ->setArguments(array_merge($this->options['args'], $args));
    }

    public function run($command = null, array $args = [], $cwd = null, array $env = [], $data = null, OutputInterface $output = null)
    {
        $proc = $this->build($command, $args)
            ->setWorkingDirectory($cwd ?: $this->options['cwd'])
            ->addEnvironmentVariables(array_merge($this->options['env'], $env))
            ->setStdin($data)
            ->getProcess();

        $callback = null;
        if (isset($output)) {
            $callback = function ($type, $buffer) use ($output) {
                $output->writeln($buffer);
            };
        }

        $proc->run($callback);

        return $proc;
    }

    public function __call($name, array $arguments)
    {
        return $this->run($name, $arguments);
    }

    public function read()
    {
        return $this->run()->getOutput();
    }

    public function write($data)
    {
        return $this->build()->write($data);
    }

    public function pipe(WriteableInterface $to)
    {


}
