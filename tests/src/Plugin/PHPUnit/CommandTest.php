<?php

namespace Task\Plugin\PHPUnit;

class CommandTest extends \PHPUnit_Framework_TestCase {
    public function testConstruct() {
        $output = $this->getMock('Symfony\Component\Console\Output\ConsoleOutput');
        $command = new Command($output);
        $this->assertEquals($output, $command->getOutput());
    }

    public function testHandleArguments() {
        $output = $this->getMock('Symfony\Component\Console\Output\ConsoleOutput');
        $command = new Command($output);

        $ref = new \ReflectionClass('Task\Plugin\PHPUnit\Command');
        $arguments = $ref->getProperty('arguments');
        $arguments->setAccessible(true);

        $command->handleArguments([]);

        $printer = $arguments->getValue($command)['printer'];
        $this->assertInstanceOf('Task\Plugin\PHPUnit\ResultPrinter', $printer);
        $this->assertInstanceOf('PHPUnit_TextUI_ResultPrinter', $printer->getPrinter());
        $this->assertEquals($output, $printer->getOutput());
    }

    public function testHandleArgumentsWrapsExistingPrinter() {
        $output = $this->getMock('Symfony\Component\Console\Output\ConsoleOutput');
        $command = new Command($output);
        $wrappedPrinter = new \PHPUnit_Util_Printer;

        $ref = new \ReflectionClass('Task\Plugin\PHPUnit\Command');
        $arguments = $ref->getProperty('arguments');
        $arguments->setAccessible(true);
        $arguments->setValue($command, array_merge($arguments->getValue($command), ['printer' => $wrappedPrinter]));
        $command->handleArguments([]);

        $printer = $arguments->getValue($command)['printer'];
        $this->assertInstanceOf('Task\Plugin\PHPUnit\ResultPrinter', $printer);
        $this->assertEquals($wrappedPrinter, $printer->getPrinter());
        $this->assertEquals($output, $printer->getOutput());
    }

}
