<?php

namespace spec\Task\Plugin\Process;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ProcessBuilderSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Task\Plugin\Process\ProcessBuilder');
    }
}
