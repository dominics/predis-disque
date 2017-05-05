<?php


namespace Predisque\Connection\Aggregate;

use Predis\Command\CommandInterface;
use Predis\Connection\ConnectionInterface;
use Predis\Connection\NodeConnectionInterface;
use Predisque\Cluster\RandomStrategy;
use Predisque\Cluster\StrategyInterface;

/**
 * The client should be given a number of IP addresses and ports where nodes are located. The client should select
 * random nodes and should try to connect until an available one is found. On a successful connection the HELLO command
 * should be used in order to retrieve the Node ID and other potentially useful information (server version, number of
 * nodes).
 *
 * If a consumer sees a high message rate received from foreign nodes, it may optionally have logic in order to
 * retrieve messages directly from the nodes where producers are producing the messages for a given topic. The consumer
 * can easily check the source of the messages by checking the Node ID prefix in the messages IDs.
 *
 * The GETJOB command, or other commands, may return a -LEAVING error instead of blocking. This error should be
 * considered by the client library as a request to connect to a different node, since the node it is connected to is
 * not able to serve the request since it is leaving the cluster. Nodes in this state have a very high priority number
 * published via HELLO, so will be unlikely to be picked at the next connection attempt.
 */
class DisqueCluster implements ClusterInterface, \IteratorAggregate, \Countable
{
    private $strategy;

    /**
     * @var ConnectionInterface[]
     */
    private $pool;

    /**
     * @var NodeConnectionInterface
     */
    private $selected = null;

    /**
     * @param StrategyInterface $strategy Optional cluster strategy.
     */
    public function __construct(StrategyInterface $strategy = null)
    {
        $this->strategy = $strategy ?? new RandomStrategy();
    }

    public function add(NodeConnectionInterface $connection)
    {
        $parameters = $connection->getParameters();

        if (isset($parameters->alias)) {
            $this->pool[$parameters->alias] = $connection;
        } else {
            $this->pool[] = $connection;
        }
    }

    public function connect()
    {
        if (empty($this->pool)) {
            $this->selected = null;
            return;
        }

        $this->selected = $this->strategy->selectInitialNode($this->pool);
        $this->selected->connect();
    }

    public function disconnect()
    {
        if ($this->selected) {
            $this->selected->disconnect();
        }
    }

    public function executeCommand(CommandInterface $command)
    {
        return $this->getConnection($command)->executeCommand($command);
    }

    public function getConnection(CommandInterface $command): ?NodeConnectionInterface
    {
        return $this->selected;
    }

    public function getConnectionById($connectionID)
    {
        return isset($this->pool[$connectionID]) ? $this->pool[$connectionID] : null;
    }

    public function isConnected()
    {
        return $this->selected && $this->selected->isConnected();
    }

    public function readResponse(CommandInterface $command)
    {
        return $this->getConnection($command)->readResponse($command);
    }

    public function remove(NodeConnectionInterface $connection)
    {
        $removed = false;

        if (($id = array_search($connection, $this->pool, true)) !== false) {
            unset($this->pool[$id]);
            $removed = true;
        }

        if ($this->selected === $connection) {
            $this->selected = null;

            // Choose another?
            // $this->setSelected($this->strategy->selectInitialNode($this->pool));
        }

        return $removed;
    }

    public function writeRequest(CommandInterface $command)
    {
        $this->getConnection($command)->writeRequest($command);
    }

    public function count()
    {
        return count($this->pool);
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->pool);
    }

    /**
     * Executes the specified Redis command on all the nodes of a cluster.
     *
     * @param CommandInterface $command A Redis command.
     *
     * @return array
     */
    public function executeCommandOnNodes(CommandInterface $command)
    {
        $responses = [];

        foreach ($this->pool as $connection) {
            // We are lazy about connecting to other nodes
            if (!$connection->isConnected()) {
                $connection->connect();
            }

            $responses[] = $connection->executeCommand($command);
        }

        return $responses;
    }

    protected function setSelected(?NodeConnectionInterface $connection): void
    {
        $this->selected = $connection;
    }
}
