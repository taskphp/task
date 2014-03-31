<?php

namespace Task\Plugin;

use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;

class PHPUnitPlugin implements PluginInterface
{
    protected $output;
    protected $arguments = [];

    public function __construct()
    {
        $this->flush();
    }

    public function run(
        $testCase = null,
        $testFile = null,
        PHPUnit\Command $command = null
    ) {
        if (!empty($testCase)) {
            $this->addArguments([$testCase]);
        }
        if (!empty($testFile)) {
            $this->addArguments([$testFile]);
        }

        $command = $command ?: new PHPUnit\Command;

        $output = $this->getOutput() ?: new ConsoleOutput;
        $command->setOutput($output);

        $retval = $command->run($this->getArguments(), false);
        $this->flush();

        return $retval;
    }

    public function flush()
    {
        $this->arguments = [];
        $this->output = null;
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

    public function getArguments()
    {
        return $this->arguments;
    }

    public function addArguments(array $arguments)
    {
        $this->arguments = array_merge($this->arguments, $arguments);

        return $this;
    }

    public function useColors()
    {
        return $this->addArguments(['--colors']);
    }

    public function setBootstrap($bootstrap)
    {
        return $this->addArguments(['--bootstrap', $bootstrap]);
    }

    public function setConfiguration($configuration)
    {
        return $this->addArguments(['--configuration', $configuration]);
    }

    public function addCoverage($coverage)
    {
        return $this->addArguments(["--coverage-$coverage"]);
    }

    public function setIniValue($key, $value)
    {
        return $this->addArguments(['-d', "$key=$value"]);
    }

    public function useDebug()
    {
        return $this->addArguments(['--debug']);
    }

    public function setFilter($filter)
    {
        return $this->addArguments(['--filter', $filter]);
    }

    public function setTestsuite($testsuite)
    {
        return $this->addArguments(['--testsuite', $testsuite]);
    }

    public function addGroups(array $groups)
    {
        return $this->addArguments(['--group', implode(',', $groups)]);
    }

    public function excludeGroups(array $groups)
    {
        return $this->addArguments(['--exclude-group', implode(',', $groups)]);
    }

    public function addTestSuffixes(array $testSuffixes)
    {
        return $this->addArguments(['--test-suffix', implode(',', $testSuffixes)]);
    }

    public function setIncludePath($includePath)
    {
        return $this->addArguments(['--include-path', $includePath]);
    }

    public function setPrinter($printer)
    {
        return $this->addArguments(['--printer', $printer]);
    }
}
