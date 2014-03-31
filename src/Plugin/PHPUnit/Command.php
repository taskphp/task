<?php

namespace Task\Plugin\PHPUnit;

use Symfony\Component\Console\Output\OutputInterface;

class Command extends \PHPUnit_TextUI_Command {
    protected $output;

    public function __construct(OutputInterface $output = null) {
        $this->output = $output;
    }

    public function setOutput(OutputInterface $output) {
        $this->output = $output;
        return $this;
    }

    public function getOutput() {
        return $this->output;
    }

    public function handleArguments(array $argv) {
        parent::handleArguments($argv);
        $this->arguments['printer'] = new ResultPrinter(
            isset($this->arguments['printer'])
                ? $this->arguments['printer']
                : new \PHPUnit_TextUI_ResultPrinter,
            $this->getOutput()
        );
    }
}
