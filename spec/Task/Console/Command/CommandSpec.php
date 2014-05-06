<?php

namespace spec\Task\Console\Command;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

use Task\Console\Input\Input;
use Task\Plugin\Console\Output\Output;
use Task\Console\Command\Command;
use Task\Project;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CommandSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('test');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Task\Console\Command\Command');
    }

    function it_should_assign_input_and_output(Input $input, Output $output)
    {
        $this->setCode(function () {});
        $this->run($input, $output);

        $this->getInput()->shouldReturn($input);
        $this->getOutput()->shouldReturn($output);
    }

    function it_should_run_a_task_on_demand(Project $project, HelperSet $helperSet, Command $command, Input $input, OutputInterface $output)
    {
        $project->get('test')->willReturn($command);
        $this->setIO($input, $output);

        $project->getHelperSet()->willReturn($helperSet);
        $this->setApplication($project);

        $project->runTask('test', $input, $output)->shouldBeCalled();

        $this->runTask('test', $output, $input);
    }

    function it_should_get_a_property(Input $input)
    {
        $input->getProperty('foo', null)->willReturn('bar');
        $this->setInput($input);
        $this->getProperty('foo')->shouldReturn('bar');
    }
}
