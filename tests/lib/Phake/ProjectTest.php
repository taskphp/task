<?php

namespace Phake;

class ProjectTest extends \PHPUnit_Framework_TestCase {
    /**
     * @dataProvider resolveDependenciesProvider
     */
    public function testResolveDependencies($tasks, $result) {
        $project = new Project('test');
        foreach ($tasks as $task) {
            $project->addTask($task[0], function() {}, $task[1] ?: []);
        }

        $this->assertEquals($result, $project->resolveDependencies(['test']));
    }
    public function resolveDependenciesProvider() {
        return [
            [
                [
                    ['test', ['dep']],
                    ['dep', null]
                ],
                ['dep', 'test']
            ],
            [
                [
                    ['test', ['dep1', 'dep2']],
                    ['dep1', null],
                    ['dep2', null]
                ],
                ['dep1', 'dep2', 'test']
            ],
            [
                [
                    ['test', ['dep1', 'dep2']],
                    ['dep1', ['dep2']],
                    ['dep2', null]
                ],
                ['dep1', 'dep2', 'test']
            ]
        ];
    }
}
