<?php

namespace spec\Task\Plugin;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class FilesystemPluginSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Task\Plugin\FilesystemPlugin');
    }

    function it_should_extend_symfony()
    {
        $this->shouldBeAnInstanceOf('Symfony\Component\Filesystem\Filesystem');
    }
}
