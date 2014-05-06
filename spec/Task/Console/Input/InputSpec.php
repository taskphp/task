<?php

namespace spec\Task\Console\Input;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

use Task\Project;
use Symfony\Component\Console\Application;

class InputSpec extends ObjectBehavior
{
    function let(Project $project)
    {
        $project->getDefinition()->willReturn((new Project('test'))->getDefinition());
        $this->beConstructedWith($project, ['task', 'test']);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Task\Console\Input\Input');
    }

    function it_should_have_a_property_option()
    {
        $definition = $this->getDefinition();
        $definition->hasOption('property')->shouldReturn(true);
        $definition->hasShortcut('p')->shouldReturn(true);
        $definition->getOption('property')->isValueRequired()->shouldReturn(true);
        $definition->getOption('property')->isArray()->shouldReturn(true);
    }

    function it_should_get_a_property(Project $project)
    {
        $this->beConstructedWith($project, ['task', 'test', '-p', 'foo=bar']);
        $this->getProperty('foo')->shouldReturn('bar');
    }

    function it_should_return_default_property()
    {
        $this->getProperty('foo', 'bar')->shouldReturn('bar');
    }

    function it_should_throw_on_no_property()
    {
        $this->shouldThrow('InvalidArgumentException')->duringGetProperty('foo');
    }
}
