<?php

namespace spec\Task;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class PluginContainerSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('Task\PluginContainer');
    }

    public function it_should_be_callable()
    {
        $this(function ($plugins) {
            $plugins['foo'] = 'bar';
        });
        $this['foo']->shouldEqual('bar');
    }
}
