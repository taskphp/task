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

    function it_should_be_a_container()
    {
        $this->shouldBeAnInstanceOf('Pimple');
    }

    function it_should_be_invokable()
    {
        $this->shouldBeAnInstanceOf('Task\InvokableContainer');
    }

    public function let()
    {
        $this->beConstructedWith('test');
    }

    public function it_should_resolve_no_dependencies()
    {
        $this->add('test', function () {});
        $this->resolveDependencies('test')->shouldEqual([]);
    }

    public function it_should_resolve_one_dependency()
    {
        $this->add('test', function () {}, ['foo']);
        $this->resolveDependencies('test')->shouldEqual(['foo']);
    }

    public function it_should_resolve_many_dependencies()
    {
        $this->add('test', function () {}, ['foo', 'bar', 'baz']);
        $this->resolveDependencies('test')->shouldEqual(['foo', 'bar', 'baz']);
    }

    public function it_should_normalize_dependencies()
    {
        $this->add('test', function () {}, ['foo', 'bar']);
        $this->add('foo', function () {}, ['bar']);
        $this->resolveDependencies('test')->shouldEqual(['foo', 'bar']);
    }

    public function it_should_normalize_complex_dependencies()
    {
        $this->add('test', function () {}, ['foo']);
        $this->add('foo', function () {}, ['bar']);
        $this->add('bar', function () {}, ['baz']);
        $this->resolveDependencies('test')->shouldEqual(['baz', 'bar', 'foo']);
    }
}
