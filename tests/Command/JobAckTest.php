<?php


namespace Predisque\Command;

class JobAckTest extends AbstractJobMultiTest
{
    protected function getExpectedCommand()
    {
        return JobAck::class;
    }

    protected function getExpectedId()
    {
        return 'ACKJOB';
    }
}
