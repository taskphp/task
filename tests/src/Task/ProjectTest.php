<?php

namespace Task;

class ProjectTest extends \PHPUnit_Framework_TestCase {
    /**
     * @dataProvider resolveDependenciesProvider
     */
    public function testResolveDependencies($tasks, $result) {
        $project = new Project('test');
        foreach ($tasks as $task) {
            $project->add($task[0], function() {}, $task[1] ?: []);
        }

        $this->assertEquals($result, $deps = $project->resolveDependencies('test'), print_r($deps, true));
    }
    public function resolveDependenciesProvider() {
        return [
            [
                [
                    ['test', ['dep']]
                ],
                ['dep']
            ],
            [
                [
                    ['test', ['dep1', 'dep2']]
                ],
                ['dep1', 'dep2']
            ],
            [
                [
                    ['test', ['dep1', 'dep2']],
                    ['dep1', ['dep2']]
                ],
                ['dep1', 'dep2']
            ],
            [
                [
                    ['test', ['dep1']],
                    ['dep1', ['dep2']],
                    ['dep2', ['dep3']]
                ],
                ['dep3', 'dep2', 'dep1']
            ]
        ];
    }
}
