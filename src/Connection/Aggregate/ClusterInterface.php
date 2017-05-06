<?php

namespace Predisque\Connection\Aggregate;

use Predis\Connection\AggregateConnectionInterface;
use Predis\Connection\NodeConnectionInterface;

interface ClusterInterface extends AggregateConnectionInterface
{
    /**
     * Use HELLO or a similar method to enumerate the members of the cluster
     */
    public function discover(): void;

    public function pickNode(): NodeConnectionInterface;

    /**
     * Switches the internal connection instance in use.
     *
     * @param string|NodeConnectionInterface $connection Alias of a connection
     */
    public function switchTo($connection): void;

    /**
     * Returns the connection instance currently in use by the aggregate
     * connection.
     *
     * @return NodeConnectionInterface
     */
    public function getCurrent(): NodeConnectionInterface;
}
