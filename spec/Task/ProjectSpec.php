<?php

namespace spec\Task;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

use Symfony\Component\Console\Input\ArrayInput;
use Task\Plugin\Console\Output\Output;
use Symfony\Component\Console\Command\Command as BaseCommand;
use Task\Console\Command\Command;

class ProjectSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Task\Project');
    }

    function let()
    {
        $this->beConstructedWith('test');
    }

    function it_should_have_a_container()
    {
        $this->getContainer()->shouldBeAnInstanceOf('Pimple');
    }

    function it_should_inject_a_container()
    {
        $this->inject(function ($container) {
            return $container;
        })->shouldReturn($this->getContainer());
    }

    function it_should_extend()
    {
    }

    function it_should_run_a_task_on_demand()
    {
        $this->addTask('test', function () {
            return 123;
        });
        $this->runTask('test')->shouldReturn(123);
    }

    function it_should_run_plain_commands()
    {
        $command = new BaseCommand('test');
        $command->setCode(function () {
            return 123;
        });

        $input = new ArrayInput(['command' => 'test']);

        $this->add($command);
        $this->run($input)->shouldReturn(123);
    }

    function it_should_run_a_task()
    {
        $this->addTask('test', function () {
            return 123;
        });

        $input = new ArrayInput(['command' => 'test']);
        $this->run($input)->shouldReturn(123);
    }

    function it_should_throw_on_no_args_to_parse()
    {
        $this->shouldThrow('InvalidArgumentException')->duringParseArguments([]);
    }

    function it_should_parse_command()
    {
        $command = new BaseCommand('test');
        $this->parseArguments([$command])->shouldReturn(
            [null, null, [], $command]
        );
    }

    function it_should_parse_name_command()
    {
        $command = new BaseCommand('test');
        $this->parseArguments(['foo', $command])->shouldReturn(
            ['foo', null, [], $command]
        );
    }

    function it_should_parse_name_task()
    {
        $work = function () {
        };
        $this->parseArguments(['test', $work])->shouldReturn(
            ['test', null, [], $work]
        );
    }

    function it_should_parse_name_description_task()
    {
        $work = function () {
        };
        $this->parseArguments(['test', 'foo', $work])->shouldReturn(
            ['test', 'foo', [], $work]
        );
    }

    function it_should_parse_name_deps_task()
    {
        $work = function () {
        };
        $this->parseArguments(['test', ['foo'], $work])->shouldReturn(
            ['test', null, ['foo'], $work]
        );
    }

    function it_should_parse_name_description_deps_task()
    {
        $work = function () {
        };
        $this->parseArguments(['test', 'foo', ['bar'], $work])->shouldReturn(
            ['test', 'foo', ['bar'], $work]
        );
    }

    function it_should_throw_on_too_few_args()
    {
        $this->shouldThrow('InvalidArgumentException')->duringParseArguments(['test']);
    }

    function it_should_add_a_command()
    {
        $command = new BaseCommand('test');
        $command->setCode(function () {
            return 123;
        });

        $this->addTask($command);
        $this->run(new ArrayInput(['command' => 'test']))->shouldReturn(123);
    }

    function it_should_alias_a_command()
    {
        $command = new BaseCommand('test');
        $command->setCode(function () {
            return 123;
        });

        $this->addTask('foo', $command);
        $this->run(new ArrayInput(['command' => 'foo']))->shouldReturn(123);
    }

    function it_should_add_a_closure()
    {
        $this->addTask('test', function () {
            return 123;
        });

        $this->run(new ArrayInput(['command' => 'test']))->shouldReturn(123);
    }

    function it_should_inject_a_closure()
    {
        $this->getContainer()['foo'] = $foo = 123;
        $this->addTask('test', ['foo', function ($foo) {
            return $foo;
        }]);

        $this->run(new ArrayInput(['command' => 'test']))->shouldReturn(123);
    }

    function it_should_add_a_group(Output $output)
    {
        $this->getContainer()['output'] = $output;
        $this->addTask('foo', ['output', function ($output) {
            $output->writeln('foo');
            return null;
        }]);

        $this->addTask('bar', function () {
            return 123;
        });

        $this->addTask('test', ['foo', 'bar']);

        $output->writeln('foo')->shouldBeCalled();
        $this->run(new ArrayInput(['command' => 'test']), $output)->shouldReturn(123);
    }

    function it_should_throw_on_bad_work()
    {
        $this->shouldThrow('InvalidArgumentException')->duringAddTask('test', new \StdClass);
    }

    function it_should_add_description()
    {
        $this->addTask('test', 'foo', function () {});
        $this->get('test')->getDescription()->shouldReturn('foo');
    }

    function it_should_resolve_no_dependencies()
    {
        $test = $this->addTask('test', function () {});
        $this->resolveDependencies($test)->shouldEqual([]);
    }

    function it_should_resolve_one_dependency()
    {
        $foo = $this->addTask('foo', function () {});
        $test = $this->addTask('test', ['foo'], function () {});
        $this->resolveDependencies($test)->shouldEqual([$foo]);
    }

    function it_should_resolve_many_dependencies()
    {
        $foo = $this->addTask('foo', function () {});
        $bar = $this->addTask('bar', function () {});
        $baz = $this->addTask('baz', function () {});
        $test = $this->addTask('test', ['foo', 'bar', 'baz'], function () {});
        $this->resolveDependencies($test)->shouldEqual([
            $foo, $bar, $baz
        ]);
    }

    function it_should_normalize_dependencies()
    {
        $test = $this->addTask('test', ['foo', 'bar'], function () {});
        $foo = $this->addTask('foo', ['bar'], function () {});
        $bar = $this->addTask('bar', function () {});
        $this->resolveDependencies($test)->shouldEqual([$foo, $bar]);
    }

    function it_should_normalize_complex_dependencies()
    {
        $test = $this->addTask('test', ['foo'], function () {});
        $foo = $this->addTask('foo', ['bar'], function () {});
        $bar = $this->addTask('bar', ['baz'], function () {});
        $baz = $this->addTask('baz', function () {});
        $this->resolveDependencies($test)->shouldEqual([$baz ,$bar, $foo]);
    }
}
