<?php

namespace Task\Plugin;

class PHPUnitPluginTest extends \PHPUnit_Framework_TestCase
{
    public function testRun()
    {
        $output = $this->getMock('Symfony\Component\Console\Output\ConsoleOutput');

        $runner = (new PHPUnitPlugin($output))
            ->useColors()
            ->setBootstrap('bootstrap.php')
            ->setConfiguration('phpunit.xml')
            ->addCoverage('text')
            ->setIniValue('foo', 'bar')
            ->useDebug()
            ->setFilter('^test')
            ->setTestsuite('suite')
            ->addGroups(['foo', 'bar'])
            ->excludeGroups(['baz', 'wow'])
            ->addTestSuffixes(['phpt'])
            ->setIncludePath('/tmp')
            ->setPrinter('TestSuiteListener');

        $command = $this->getMock('Task\Plugin\PHPUnit\Command', ['run'], [$output]);
        $command->expects($this->once())->method('run')
            ->with([
                '--colors',
                '--bootstrap', 'bootstrap.php',
                '--configuration', 'phpunit.xml',
                '--coverage-text',
                '-d', 'foo=bar',
                '--debug',
                '--filter', '^test',
                '--testsuite', 'suite',
                '--group', 'foo,bar',
                '--exclude-group', 'baz,wow',
                '--test-suffix', 'phpt',
                '--include-path', '/tmp',
                '--printer', 'TestSuiteListener',
                'TestCase',
                'TestCase.php'
            ]);

        $runner->run('TestCase', 'TestCase.php', $command);
    }

    public function testSetOutput()
    {
        $runner = new PHPUnitPlugin;

        $output = $this->getMock('Symfony\Component\Console\Output\ConsoleOutput');
        $runner->setOutput($output);
        $this->assertEquals($output, $runner->getOutput());
    }
}
