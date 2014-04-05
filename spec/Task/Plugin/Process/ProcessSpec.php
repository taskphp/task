<?php

namespace spec\Task\Plugin\Process;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Task\Plugin\Stream\WritableInterface;

class ProcessSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('whoami');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Task\Plugin\Process\Process');
    }

    function it_should_throw_on_run_it_non_zero_exit_code()
    {
        $this->setCommandLine('exit 1');
        $this->shouldThrow('RuntimeException')->duringRun();
    }

    function it_should_return_exit_code_on_run()
    {
        $this->setCommandLine('exit 0');
        $this->run()->shouldEqual(0);
    }

    function it_should_run_and_get_output_on_read()
    {
        $this->read()->shouldEqual(`whoami`);
    }

    function it_should_write_read_data_on_pipe(WritableInterface $to)
    {
        $to->write(`whoami`)->willReturn('me');
        $this->pipe($to)->shouldEqual('me');
    }

    function it_should_set_stdin_on_write_and_return_this()
    {
        $this->write('test')->shouldReturn($this);
        $this->getStdin()->shouldReturn('test');
    }
}
