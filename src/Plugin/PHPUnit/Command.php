<?php

namespace Task\Plugin\PHPUnit;

class Command extends \PHPUnit_TextUI_Command
{
    protected $args = [];
    protected $testCase;
    protected $testFile;

    public function run(array $argv = [], $exit = false)
    {
        return parent::run($this->getArguments(), false);
    }

    public function setTestCase($testCase)
    {
        $this->testCase = $testCase;
        return $this;
    }

    public function setTestFile($testFile)
    {
        $this->testFile = $testFile;
        return $this;
    }

    public function read()
    {
        ob_start();
        $this->run();
        $output = ob_end_clean();
        return $output;
    }

    public function pipe(WritableInterface $to)
    {
        return $to->write($this->read());
    }

    public function addArguments(array $arguments)
    {
        $this->args = array_merge($this->args, $arguments);

        return $this;
    }

    public function getArguments()
    {
        $arguments = $this->args;

        if ($this->testCase) {
            $arguments[] = $this->testCase;
        }

        if ($this->testFile) {
            $arguments[] = $this->testFile;
        }

        return $arguments;
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
