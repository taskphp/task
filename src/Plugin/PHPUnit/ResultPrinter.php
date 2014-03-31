<?php

namespace Task\Plugin\PHPUnit;

use Symfony\Component\Console\Output\OutputInterface;

class ResultPrinter extends \PHPUnit_TextUI_ResultPrinter implements \PHPUnit_Framework_TestListener {
    protected $printer;
    protected $output;

    public function __construct(\PHPUnit_Util_Printer $printer, OutputInterface $output, $verbose = false, $colors = false, $debug = false) {
        $this->printer = $printer;
        $this->output = $output;

        $this->verbose = $verbose;
        $this->colors = $colors;
        $this->debug = $debug;
    }

    public function getPrinter() {
        return $this->printer;
    }

    public function getOutput() {
        return $this->output;
    }
    
    public function write($buffer) {
        ob_start();
        $this->printer->write($buffer);
        $buffer = ob_get_contents();
        ob_end_clean();

        $this->output->write($buffer);
    }
}
