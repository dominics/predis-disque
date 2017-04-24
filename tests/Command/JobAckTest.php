<?php


namespace Predisque\Command;

class JobAckTest extends AbstractJobMultiTest
{
    /**
     * @group disconnected
     */
    public function testParseResponse()
    {
        $command = $this->getCommand();

        $this->assertSame(10, $command->parseResponse(10));
    }

    protected function getExpectedCommand()
    {
        return JobAck::class;
    }

    protected function getExpectedId()
    {
        return 'ACKJOB';
    }
}
