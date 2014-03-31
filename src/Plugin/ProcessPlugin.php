<?php

namespace Task\Plugin;

use Task\Plugin\Process\ProcessBuilder;
use Symfony\Component\Console\Output\OutputInterface;

class ProcessPlugin implements PluginInterface, WritableInterface
{
    public function build($command, array $args = [])
    {
        return ProcessBuilder::create()
            ->setPrefix($command)
            ->setArguments($args);
    }

    public function run($command, array $args = [], $cwd = null, array $env = [], OutputInterface $output = null)
    {
        $proc = $this->build($command, $args)
            ->setWorkingDirectory($cwd)
            ->addEnvironmentVariables($env)
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
}
