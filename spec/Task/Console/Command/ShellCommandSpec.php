<?php

namespace spec\Task\Console\Command;

use PhpSpec\ObjectBehavior;
use Symfony\Component\Console\Shell;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Application;

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

    function it_should_wrap_getApplication_in_a_shell(Application $app, HelperSet $helperSet)
    {
        $app->getHelperSet()->willReturn($helperSet);
        $app->getName()->willReturn('test');
        $this->setApplication($app);

        $shell = $this->getShell();
        $shell->shouldHaveType('Symfony\Component\Console\Shell');
    }

    function it_should_throw_during_getShell_on_no_application()
    {
        $this->shouldThrow('RuntimeException')->duringGetShell();
    }
}
