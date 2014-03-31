<?php

namespace spec\Task\Plugin\PhpSpec\Console;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\Console\Application;

class CommandRunnerSpec extends ObjectBehavior
{
    function let(Application $app)
    {
        $this->beConstructedWith($app, 'run');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Task\Plugin\PhpSpec\Console\CommandRunner');
    }
}
