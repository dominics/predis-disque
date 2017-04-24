<?php


namespace Predisque\Command;

class JobDequeueTest extends AbstractJobMultiTest
{
    protected function getExpectedCommand()
    {
        return JobDequeue::class;
    }

    protected function getExpectedId()
    {
        return 'DEQUEUE';
    }
}
