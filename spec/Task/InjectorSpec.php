<?php

namespace spec\Task;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

use Symfony\Component\Console\Output\OutputInterface;

class InjectorSpec extends ObjectBehavior
{
    function let(\Pimple $container)
    {
        $this->beConstructedWith($container);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Task\Injector');
    }

    function it_should_call_function_with_services(OutputInterface $output)
    {
        $container = new \Pimple([
            'foo' => 'baz',
            'bar' => 'wow'
        ]);
        $this->beConstructedWith($container);

        $this(['foo', 'bar', function () {
            return func_get_args();
        }])->shouldReturn(['baz', 'wow']);
    }
}

interface Invokable
{
    public function __invoke();
}
