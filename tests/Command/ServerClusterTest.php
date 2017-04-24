<?php

namespace Predisque\Command;

class ServerClusterTest extends CommandTestCase
{
    protected function getExpectedId()
    {
        return 'CLUSTER';
    }

    protected function getExpectedCommand()
    {
        return ServerCluster::class;
    }
}
