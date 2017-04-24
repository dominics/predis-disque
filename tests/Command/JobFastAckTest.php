<?php


namespace Predisque\Command;

class JobFastAckTest extends AbstractJobMultiTest
{
    protected function getExpectedCommand()
    {
        return JobFastAck::class;
    }

    protected function getExpectedId()
    {
        return 'FASTACK';
    }
}
