<?php

namespace Task\Console\Command;

use Symfony\Component\Console\Shell;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ShellCommand extends Command
{
    protected $shell;

    public function __construct(Shell $shell = null)
    {
        parent::__construct();
        $this->shell = $shell;
    }

    public function configure()
    {
        $this->setName('shell');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $shell = $this->shell ?: new Shell($this->getApplication());
        return $shell->run();
    }
}
