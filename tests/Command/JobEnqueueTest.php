<?php


namespace Predisque\Command;

class JobEnqueueTest extends AbstractJobMultiTest
{
    protected function getExpectedCommand()
    {
        return JobEnqueue::class;
    }

    protected function getExpectedId()
    {
        return 'ENQUEUE';
    }
}
