<?php

namespace spec\Task\Plugin\Console;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Input\InputDefinition;

class ApplicationPluginSpec extends ObjectBehavior
{
    function let(Application $app, HelperSet $helpers, InputDefinition $definition)
    {
        $app->get('test')->willReturn(new Command('test'));
        $app->getHelperSet()->willReturn($helpers);
        $definition->getArguments()->willReturn([]);
        $definition->getOptions()->willReturn([]);
        $app->getDefinition()->willReturn($definition);
        $this->beConstructedWith($app);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Task\Plugin\Console\ApplicationPlugin');
    }

    function it_should_init_a_command_runner()
    {
        $runner = $this->command('test');
        $runner->shouldHaveType('Task\Plugin\Console\CommandRunner');
    }
}
