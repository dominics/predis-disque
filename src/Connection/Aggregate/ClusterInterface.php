<?php

namespace Predisque\Connection\Aggregate;

use Predis\Command\CommandInterface;
use Predis\Connection\AggregateConnectionInterface;
use Predis\Connection\NodeConnectionInterface;

/**
 * Disque cluster interface
 */
interface ClusterInterface extends AggregateConnectionInterface
{
    /**
     * @param CommandInterface $command
     * @return mixed
     */
    public function executeCommandOnCluster(CommandInterface $command);

    /**
     * @param CommandInterface $command
     * @return mixed
     */
    public function executeCommandOnPool(CommandInterface $command);

    /**
     * Returns the connection instance in charge for the given command.
     *
     * @param CommandInterface $command Command instance.
     *
     * @return NodeConnectionInterface
     */
    public function getConnection(CommandInterface $command);

    /**
     * Returns a connection instance from the aggregate connection by its alias.
     *
     * @param string $connectionID Connection alias.
     *
     * @return NodeConnectionInterface|null
     */
    public function getConnectionById($connectionID);
}
