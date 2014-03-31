<?php

namespace spec\Task\Plugin;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class PhpSpecPluginSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Task\Plugin\PhpSpecPlugin');
    }
}
