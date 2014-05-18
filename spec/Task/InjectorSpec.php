<?php

namespace spec\Task;

use PhpSpec\ObjectBehavior;
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

        $work = $this(['foo', 'bar', function () {
            return func_get_args();
        }]);
        $work()->shouldReturn(['baz', 'wow']);
    }

    function it_should_throw_on_not_callable()
    {
        $this->shouldThrow('InvalidArgumentException')->during('__invoke', [['foo', 'bar', new \StdClass]]);
    }
}
