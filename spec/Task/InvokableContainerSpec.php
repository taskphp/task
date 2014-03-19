<?php

namespace spec\Task;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class InvokableContainerSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('Task\InvokableContainer');
    }

    public function it_should_be_callable()
    {
        $this(function ($plugins) {
            $plugins['foo'] = 'bar';
        });
        $this['foo']->shouldEqual('bar');
    }
}
