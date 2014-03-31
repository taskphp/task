<?php

namespace Task\Plugin\PHPUnit;

class ResultPrinterTest extends \PHPUnit_Framework_TestCase {
    public function testConstruct() {
        $printer = new \PHPUnit_Util_Printer;
        $output = $this->getMock('Symfony\Component\Console\Output\ConsoleOutput');
        $resultPrinter = new ResultPrinter($printer, $output);

        $this->assertEquals($printer, $resultPrinter->getPrinter());
        $this->assertEquals($output, $resultPrinter->getOutput());
    }

    public function testWrite() {
        $printer = $this->getMock('PHPUnit_Util_Printer', ['write']);
        $output = $this->getMock('Symfony\Component\Console\Output\ConsoleOutput', ['write']);
        $resultPrinter = new ResultPrinter($printer, $output);
        
        $buffer = 'foo';
        $printer->expects($this->once())->method('write')
            ->with($buffer)
            ->will($this->returnCallback(function($buffer) {
                echo $buffer;
            }));
        $output->expects($this->once())->method('write')
            ->with($buffer);

        $resultPrinter->write($buffer);
    }
}
