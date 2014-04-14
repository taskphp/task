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
        $test = $this->addTask('test', function () {});
        $this->resolveDependencies($test)->shouldEqual([]);
    }

    public function it_should_resolve_one_dependency()
    {
        $foo = $this->addTask('foo', function () {});
        $test = $this->addTask('test', function () {}, ['foo']);
        $this->resolveDependencies($test)->shouldEqual([$foo]);
    }

    public function it_should_resolve_many_dependencies()
    {
        $foo = $this->addTask('foo', function () {});
        $bar = $this->addTask('bar', function () {});
        $baz = $this->addTask('baz', function () {});
        $test = $this->addTask('test', function () {}, ['foo', 'bar', 'baz']);
        $this->resolveDependencies($test)->shouldEqual([
            $foo, $bar, $baz
        ]);
    }

    public function it_should_normalize_dependencies()
    {
        $test = $this->addTask('test', function () {}, ['foo', 'bar']);
        $foo = $this->addTask('foo', function () {}, ['bar']);
        $bar = $this->addTask('bar', function () {});
        $this->resolveDependencies($test)->shouldEqual([$foo, $bar]);
    }

    public function it_should_normalize_complex_dependencies()
    {
        $test = $this->addTask('test', function () {}, ['foo']);
        $foo = $this->addTask('foo', function () {}, ['bar']);
        $bar = $this->addTask('bar', function () {}, ['baz']);
        $baz = $this->addTask('baz', function () {});
        $this->resolveDependencies($test)->shouldEqual([$baz ,$bar, $foo]);
    }
}
