<?php

namespace Task\Console\Command;

use Symfony\Component\Console\Shell;
use Symfony\Component\Console\Command\Command as BaseCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ShellCommand extends BaseCommand
{
    protected $shell;

    public function __construct(Shell $shell = null)
    {
        parent::__construct();
        $this->shell = $shell;
    }

    public function configure()
    {
        $this
            ->setName('shell')
            ->setDescription('Runs a shell');
    }

    public function getShell()
    {
        if (!$shell = $this->shell) {
            if ($app = $this->getApplication()) {
                    $shell = new Shell($app);
            } else {
                    throw new \RuntimeException("Couldn't find an application");
            }
        }

        return $shell;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        return $this->getShell()->run();
    }
}
