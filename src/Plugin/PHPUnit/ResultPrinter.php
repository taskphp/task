<?php

namespace Task\Plugin\PHPUnit;

use Task\Plugin\Stream\WritableInterface;

class ResultPrinter extends \PHPUnit_TextUI_ResultPrinter implements \PHPUnit_Framework_TestListener
{
    protected $printer;
    protected $output;

    public function __construct(WritableInterface $output, \PHPUnit_Util_Printer $printer = null)
    {
        $this->output = $output;
        $this->printer = $printer;
    }

    public function setPrinter(\PHPUnit_Util_Printer $printer)
    {
        $this->printer = $printer;
        return $this;
    }

    public function getPrinter()
    {
        return $this->printer;
    }

    public function getOutput()
    {
        return $this->output;
    }

    public function write($buffer)
    {
        ob_start();
        $this->printer->write($buffer);
        $buffer = ob_get_clean();

        $this->output->write($buffer);
    }
}
