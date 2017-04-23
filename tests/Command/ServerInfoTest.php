<?php

namespace Varspool\Disque\Command;

class ServerInfoTest extends CommandTestCase
{
    protected function getExpectedId()
    {
        return 'INFO';
    }

    protected function getExpectedCommand()
    {
        return ServerInfo::class;
    }

    /**
     * @group disconnected
     */
    public function testFilterArguments()
    {
        $command = $this->getCommand();
        $command->setArguments(array());

        $this->assertSame(array(), $command->getArguments());
    }
}
