<?php

namespace spec\Task\Plugin\Console;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\HelperSet;

class CommandRunnerSpec extends ObjectBehavior
{
    private $definition;

    function let(Application $app, Command $command, HelperSet $helpers)
    {
        $this->definition = new InputDefinition([
            new InputArgument('arg', InputArgument::OPTIONAL),
            new InputOption('opt', 'o', InputOption::VALUE_REQUIRED)
        ]);
        $command->getDefinition()->willReturn($this->definition);
        $command->getName()->willReturn('test');
        $command->setApplication($app)->willReturn();
        $command->mergeApplicationDefinition()->willReturn();

        $app->get('test')->willReturn($command);
        $app->getHelperSet()->willReturn($helpers);

        $this->beConstructedWith($app, 'test');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Task\Plugin\Console\CommandRunner');
    }

    function it_should_parse_method_names()
    {
        $this->parseMethodName('FooBarBaz')->shouldReturn('foo-bar-baz');
    }

    function it_should_add_an_argument()
    {
        $this->setArg('test')->shouldReturn($this);
        $this->getParameters()->shouldReturn([
            'arg' => 'test'
        ]);
    }

    function it_should_add_a_long_option()
    {
        $this->setOpt('test')->shouldReturn($this);
        $this->getParameters()->shouldReturn([
            '--opt' => 'test'
        ]);
    }

    function it_should_throw_on_unrecognised_parameter()
    {
        $this->shouldThrow('InvalidArgumentException')->duringSetFoo('test');
    }

    function it_should_run_the_application(
        Application $app,
        Command $command,
        OutputInterface $output
    ) {
        $this->setArg('test');
        $this->setOpt('test');

        $definition = $this->definition;
        $definition->addArgument(new InputArgument('command'));

        $input = Argument::that(function ($input) use ($definition) {
            $input->bind($definition);

            return $input instanceof ArrayInput
                && $input->getArguments() == [
                        'command' => 'test',
                        'arg' => 'test'
                    ]
                && $input->getOptions() == [
                        'opt' => 'test'
                    ];
        });

        $app->run($input, $output)->shouldBeCalled();
        $this->run($output);
    }
}
