<?php

namespace spec\Task\Console\Command;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

use Symfony\Component\Console\Input\Input;
use Task\Plugin\Console\Output\Output;

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

    function it_should_have_a_property_option()
    {
        $definition = $this->getDefinition();
        $definition->hasOption('property')->shouldReturn(true);
        $definition->hasShortcut('p')->shouldReturn(true);
        $definition->getOption('property')->isValueRequired()->shouldReturn(true);
        $definition->getOption('property')->isArray()->shouldReturn(true);
    }

    function it_should_assign_input_and_output(Input $input, Output $output)
    {
        $this->setCode(function () {});
        $this->run($input, $output);

        $this->getInput()->shouldReturn($input);
        $this->getOutput()->shouldReturn($output);
    }

    function it_should_get_a_property(Input $input)
    {
        $input->getOption('property')->willReturn(['foo=bar']);
        $this->getProperty('foo', $input)->shouldReturn('bar');
    }

    function it_should_get_an_existing_property(Input $input)
    {
        $this->setProperty('foo', 'bar');
        $this->getProperty('foo', $input)->shouldReturn('bar');
    }

    function it_should_no_on_no_property(Input $input)
    {
        $input->getOption('property')->willReturn([]);
        $this->shouldThrow('InvalidArgumentException')->duringGetProperty('foo', $input);
    }
}
