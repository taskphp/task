<?php

namespace spec\Task\Console\Command;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

use Symfony\Component\Console\Shell;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ShellCommandSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Task\Console\Command\ShellCommand');
    }

    function it_should_run_a_shell(Shell $shell, InputInterface $input, OutputInterface $output)
    {
        $this->beConstructedWith($shell);
        $shell->run()->willReturn(0);
        $this->run($input, $output)->shouldReturn(0);
    }
}
