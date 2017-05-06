<?php

namespace Predisque\Cluster;

use Predis\Connection\NodeConnectionInterface;
use Predisque\Connection\Parameters;

interface StrategyInterface
{
    /**
     * Pick an initial connection to use (before making a HELLO request) from a set of connections
     *
     * @param array $connections
     * @return NodeConnectionInterface
     */
    public function pickInitialConnection(array $connections): NodeConnectionInterface;

    /**
     * Pick a set of connection parameters to use from a HELLO response
     *
     * @param array $nodes Response from HELLO: array of arrays, [[string $id, string $host, int $port, int $priority]]
     * @return Parameters The connection parameters to use for the next connection to this cluster
     */
    public function pickNodeFromHello(array $nodes): Parameters;
}
