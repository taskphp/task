<?php

namespace spec\Task;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ProjectSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Task\Project');
    }

    public function let()
    {
        $this->beConstructedWith('test');
    }

    function it_should_have_a_container()
    {
        $this->getContainer()->shouldBeAnInstanceOf('Pimple');
    }


    public function it_should_resolve_no_dependencies()
    {
        $this->addTask('test', function () {});
        $this->resolveDependencies('test')->shouldEqual([]);
    }

    public function it_should_resolve_one_dependency()
    {
        $this->addTask('test', function () {}, ['foo']);
        $this->resolveDependencies('test')->shouldEqual(['foo']);
    }

    public function it_should_resolve_many_dependencies()
    {
        $this->addTask('test', function () {}, ['foo', 'bar', 'baz']);
        $this->resolveDependencies('test')->shouldEqual(['foo', 'bar', 'baz']);
    }

    public function it_should_normalize_dependencies()
    {
        $this->addTask('test', function () {}, ['foo', 'bar']);
        $this->addTask('foo', function () {}, ['bar']);
        $this->resolveDependencies('test')->shouldEqual(['foo', 'bar']);
    }

    public function it_should_normalize_complex_dependencies()
    {
        $this->addTask('test', function () {}, ['foo']);
        $this->addTask('foo', function () {}, ['bar']);
        $this->addTask('bar', function () {}, ['baz']);
        $this->resolveDependencies('test')->shouldEqual(['baz', 'bar', 'foo']);
    }
}
