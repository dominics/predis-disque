<?php


namespace Predisque\Connection\Aggregate;

use Predis\Command\CommandInterface;
use Predis\Connection\NodeConnectionInterface;
use Predis\Response\ErrorInterface as ResponseErrorInterface;
use Predisque\ClientException;
use Predisque\Cluster\RandomStrategy;
use Predisque\Cluster\StrategyInterface;
use Predisque\Connection\ConnectionException;
use Predisque\Connection\Factory;

/**
 * Disque cluster aggregate connection
 *
 * Here's the initial description of what a Disque client should do:
 *
 *   The client should be given a number of IP addresses and ports where nodes are located. The client should select
 *   random nodes and should try to connect until an available one is found. On a successful connection the HELLO
 *   command should be used in order to retrieve the Node ID and other potentially useful information (server version,
 *   number of nodes).
 *
 *   If a consumer sees a high message rate received from foreign nodes, it may optionally have logic in order to
 *   retrieve messages directly from the nodes where producers are producing the messages for a given topic. The
 *   consumer can easily check the source of the messages by checking the Node ID prefix in the messages IDs.
 *
 *   The GETJOB command, or other commands, may return a -LEAVING error instead of blocking. This error should be
 *   considered by the client library as a request to connect to a different node, since the node it is connected to is
 *   not able to serve the request since it is leaving the cluster. Nodes in this state have a very high priority
 *   number
 *   published via HELLO, so will be unlikely to be picked at the next connection attempt.
 *
 * Here's how we translate that into our connection implementation:
 *
 *   - The aggregate connection gets a list of nodes it should be able to connect to, these
 *     are stored in $pool
 *   - It performs discovery on one of those nodes (selected by the given strategy, default random),
 *     this node is stored in $selected
 *   -
 *   - It merges entries from
 */
class DisqueCluster implements ClusterInterface, \IteratorAggregate, \Countable
{
    /**
     * @var StrategyInterface
     */
    private $strategy;

    /**
     * @var NodeConnectionInterface[]
     */
    private $pool;

    /**
     * @var NodeConnectionInterface
     */
    private $selected = null;

    /**
     * @var Factory
     */
    private $connectionFactory;

    /**
     * @var bool
     */
    private $autoDiscovery = true;

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

        $this->selected = $this->strategy->pickNode($this->pool);


        $this->selected->connect();
    }

    public function disconnect()
    {
        if ($this->selected) {
            $this->selected->disconnect();
        }
    }

    public function discover(): void
    {
        if (!$this->connectionFactory) {
            throw new ClientException('Discovery requires a connection factory');
        }

        $nodes = $this->pool;

        foreach ($nodes as $connection) {
            try {
                $this->discoverFromNode($connection, $this->connectionFactory);
            } catch (ConnectionException $exception) {
                $this->remove($connection);
                continue;
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function executeCommand(CommandInterface $command)
    {
        return $this->retryCommandOnFailure($command, __FUNCTION__);
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

    public function pickNode(): NodeConnectionInterface
    {
        return $this->strategy->pickNode($this->pool);
    }

    /**
     * {@inheritdoc}
     */
    public function readResponse(CommandInterface $command)
    {
        return $this->retryCommandOnFailure($command, __FUNCTION__);
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

    /**
     * {@inheritdoc}
     */
    public function writeRequest(CommandInterface $command)
    {
        $this->retryCommandOnFailure($command, __FUNCTION__);
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

    protected function discoverFromNode($connection, $connectionFactory)
    {
        $response = $connection->executeCommand(RawCommand::create('HELLO'));
        $replication = $this->handleInfoResponse($response);

        if ($replication['role'] !== 'master') {
            throw new ClientException("Role mismatch (expected master, got slave) [$connection]");
        }

        $this->slaves = array();

        foreach ($replication as $k => $v) {
            $parameters = null;

            if (strpos($k, 'slave') === 0 && preg_match('/ip=(?P<host>.*),port=(?P<port>\d+)/', $v, $parameters)) {
                $slaveConnection = $connectionFactory->create(array(
                    'host' => $parameters['host'],
                    'port' => $parameters['port'],
                ));

                $this->add($slaveConnection);
            }
        }
    }

    /**
     * Handles response from HELLO.
     *
     * @param string $response
     *
     * @return array
     */
    private function handleHelloResponse($response)
    {
        $info = array();

        foreach (preg_split('/\r?\n/', $response) as $row) {
            if (strpos($row, ':') === false) {
                continue;
            }

            list($k, $v) = explode(':', $row, 2);
            $info[$k] = $v;
        }

        return $info;
    }

    /**
     * Retries the execution of a command upon a LEAVING response
     *
     * @param CommandInterface $command Command instance.
     * @param string           $method  Actual method.
     * @return mixed
     */
    private function retryCommandOnFailure(CommandInterface $command, $method)
    {
        RETRY_COMMAND:

        try {
            $connection = $this->getConnection($command);
            $response = $connection->$method($command);

            if ($response instanceof ResponseErrorInterface && $response->getErrorType() === 'LOADING') {
                throw new ConnectionException($connection, "Disque is loading the dataset in memory [$connection]");
            }

            if ($response instanceof ResponseErrorInterface && $response->getErrorType() === 'LEAVING') {
                throw new ConnectionException($connection, "Disque node is leaving the cluster [$connection]");
            }
        } catch (ConnectionException $exception) {
            $connection = $exception->getConnection();
            $connection->disconnect();
            $this->remove($connection);

            if ($this->autoDiscovery) {
                $this->discover();
            }

            goto RETRY_COMMAND;
        }

        return $response;
    }

    /**
     * Switches the internal connection instance in use.
     *
     * @param string|NodeConnectionInterface $connection Alias of a connection
     */
    public function switchTo($connection): void
    {
        // TODO: Implement switchTo() method.
    }

    /**
     * Returns the connection instance currently in use by the aggregate
     * connection.
     *
     * @return NodeConnectionInterface
     */
    public function getCurrent(): NodeConnectionInterface
    {
        // TODO: Implement getCurrent() method.
    }
}
