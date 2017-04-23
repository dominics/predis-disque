<?php

namespace Varspool\Disque\Command;

/**
 * @see \Predis\Command\ServerConfigTest
 */
class ServerDebugTest extends CommandTestCase
{
    /**
     * @group disconnected
     */
    public function testFilterArguments()
    {
        $arguments = ['FLUSHALL'];
        $expected = ['FLUSHALL'];

        $command = $this->getCommand();
        $command->setArguments($arguments);

        $this->assertSame($expected, $command->getArguments());
    }

    /**
     * @group disconnected
     */
    public function testParseResponseOfConfigSet()
    {
        $this->assertSame('OK', $this->getCommand()->parseResponse('OK'));
    }

    /**
     * @group disconnected
     */
    public function testParseResponseOfConfigResetstat()
    {
        $this->assertSame('OK', $this->getCommand()->parseResponse('OK'));
    }

    protected function getExpectedId()
    {
        return 'DEBUG';
    }

    protected function getExpectedCommand()
    {
        return ServerDebug::class;
    }
}
