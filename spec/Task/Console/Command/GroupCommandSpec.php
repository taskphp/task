<?php

namespace spec\Task\Console\Command;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Task\Project;
use Task\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GroupCommandSpec extends ObjectBehavior
{
    private $project;

    function let()
    {
        $this->project = new Project('test');
        $this->beConstructedWith('test', $this->project, ['foo', 'bar']);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Task\Console\Command\GroupCommand');
    }

    function it_should_resolve_dependencies()
    {
        $foo = $this->project->addTask('foo', function () {});
        $bar = $this->project->addTask('bar', ['baz'], function () {});
        $baz = $this->project->addTask('baz', function () {});

        $this->getCommands()->shouldReturn([$foo, $baz, $bar]);
    }

    function it_should_filter_dependencies()
    {
        $this->beConstructedWith('test', $this->project, ['baz', 'foo', 'bar']);

        $foo = $this->project->addTask('foo', function () {});
        $bar = $this->project->addTask('bar', ['baz'], function () {});
        $baz = $this->project->addTask('baz', function () {});

        $this->getCommands()->shouldReturn([$baz, $foo, $bar]);
    }

    function it_should_run_commands(Project $project, Command $foo, Command $bar, InputInterface $input, OutputInterface $output)
    {
        $foo->getName()->willReturn('foo');
        $bar->getName()->willReturn('bar');

        $project->resolveDependencies(Argument::any())->willReturn([]);
        $project->get('foo')->willReturn($foo);
        $project->get('bar')->willReturn($bar);

        $this->beConstructedWith('test', $project, ['foo', 'bar']);

        $foo->run($input, $output)->shouldBeCalled();
        $bar->run($input, $output)->shouldBeCalled();

        $this->execute($input, $output);
    }
}
