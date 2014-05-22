<?php

namespace spec\Task\Console\Command;

use PhpSpec\ObjectBehavior;
use Task\Plugin\Console\Output\Output;
use Task\Plugin\Console\Output\ProxyOutput;
use Task\Console\Command\Command;
use Task\Project;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Application;

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

    function it_should_assign_input_and_output(InputInterface $input, Output $output)
    {
        $this->setCode(function () {});
        $this->run($input, $output);

        $this->getInput()->shouldReturn($input);
        $this->getOutput()->shouldReturn($output);
    }

    function it_should_run_a_task_on_demand(Project $project, HelperSet $helperSet, Command $command, InputInterface $input, Output $output)
    {
        $project->get('test')->willReturn($command);
        $this->setIO($input, $output);

        $project->getHelperSet()->willReturn($helperSet);
        $this->setApplication($project);

        $project->runTask('test', $input, $output)->shouldBeCalled();

        $this->runTask('test', $output, $input);
    }

    function it_should_throw_during_runTask_on_no_application()
    {
        $this->shouldThrow('RuntimeException')->duringRunTask('test');
    }

    function it_should_throw_during_runTask_on_plain_application(Application $application, HelperSet $helperSet)
    {
        $application->getHelperSet()->willReturn($helperSet);
        $this->setApplication($application);
        $this->shouldThrow('RuntimeException')->duringRunTask('test');
    }

    function it_should_parse_and_get_a_property(InputInterface $input)
    {
        $input->hasOption('property')->willReturn(true);
        $input->getOption('property')->willReturn(['foo=bar']);

        $this->parseProperties($input);
        $this->getProperty('foo')->shouldReturn('bar');
    }

    function it_should_return_default_property(InputInterface $input)
    {
        $input->hasOption('property')->willReturn(true);
        $input->getOption('property')->willReturn([]);

        $this->parseProperties($input);
        $this->getProperty('foo', 'bar')->shouldReturn('bar');
    }

    function it_should_throw_on_no_property(InputInterface $input)
    {
        $input->hasOption('property')->willReturn(true);
        $input->getOption('property')->willReturn([]);

        $this->parseProperties($input);
        $this->shouldThrow('InvalidArgumentException')->duringGetProperty('foo');
    }

    function it_should_throw_on_no_properties()
    {
        $this->shouldThrow('RuntimeException')->duringGetProperty('foo');
    }
}
