<?php

namespace Task\Plugin;

class PHPUnitPluginTest extends \PHPUnit_Framework_TestCase
{
    public function testGetCommand()
    {
        $plugin = new PHPUnitPlugin;
        $command = $plugin->getCommand();
        $this->assertInstanceOf('Task\Plugin\PHPUnit\Command', $command);
    }
}
