<?php

namespace Task\Plugin\Mocha;

use Task\Plugin\Process\AbstractCommand;

class PhantomJsCommand extends AbstractCommand
{
    protected $options = [];
    protected $args = [];

    public function __construct($prefix = 'mocha-phantomjs', $cwd = '.')
    {
        $this->setPrefix($prefix);
        $this->setWorkingDirectory($cwd);
    }

    public function setPage($page)
    {
        return $this->addArgument($page);
    }
}
