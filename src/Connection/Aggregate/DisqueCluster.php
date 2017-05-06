<?php


namespace Predisque\Connection\Aggregate;

use Predis\Command\CommandInterface;
use Predis\Connection\ConnectionException as BaseConnectionException;
use Predis\Connection\FactoryInterface;
use Predis\Connection\NodeConnectionInterface;
use Predis\Response\ErrorInterface as ResponseErrorInterface;
use Predisque\ClientException;
use Predisque\Cluster\RandomStrategy;
use Predisque\Cluster\StrategyInterface;
use Predisque\Command\ServerHello;
use Predisque\Connection\ConnectionException;
use Predisque\Connection\Factory;
use Predisque\Connection\Parameters;

/**
 * Disque cluster aggregate connection
 *
 * @todo: implement optional server-switching
 *
 *   If a consumer sees a high message rate received from foreign nodes, it may optionally have logic in order to
 *   retrieve messages directly from the nodes where producers are producing the messages for a given topic. The
 *   consumer can easily check the source of the messages by checking the Node ID prefix in the messages IDs.
 */
class DisqueCluster implements ClusterInterface, \IteratorAggregate, \Countable
{
    /**
     * The currently selected connection, if there is one
     *
     * If there isn't, call getConnection() and the strategy will be consulted to pull one from the pool
     *
     * @var null|NodeConnectionInterface
     */
    private $selected = null;

    /**
     * The nodeId of the currently selected connection according to HELLO
     *
     * @var null|string
     */
    private $selectedId = null;

    /**
     * Node connection details for other nodes in the cluster
     *
     * @var Parameters[] indexed by string nodeId
     */
    private $clusterNodes = [];

    /**
     * Connection instances that can be used for an initial HELLO or to run a command on all nodes in a cluster
     *
     * @var NodeConnectionInterface[] indexed by int
     */
    private $pool;

    /**
     * A connection factory for making individual node connections in the cluster
     *
     * @var Factory
     */
    private $connectionFactory;

    /**
     * @var bool
     */
    private $autodiscover;

    /**
     * A strategy instance for deciding between nodes
     *
     * @var StrategyInterface
     */
    private $strategy;

    /**
     * @param FactoryInterface  $connectionFactory Connection factory used to create discovered connections
     * @param bool              $autodiscover
     * @param StrategyInterface $strategy          Optional cluster strategy.
     */
    public function __construct(FactoryInterface $connectionFactory, bool $autodiscover = true, StrategyInterface $strategy = null)
    {
        $this->connectionFactory = $connectionFactory;
        $this->autodiscover = $autodiscover;
        $this->strategy = $strategy ?? new RandomStrategy();
    }

    /**
     * Adds a connection instance to the aggregate connection.
     *
     * @param NodeConnectionInterface $connection Connection instance.
     */
    public function add(NodeConnectionInterface $connection)
    {
        $this->pool[] = $connection;
    }

    /**
     * Connect to a Disque cluster
     *
     * Here's the upstream description of what a Disque client should do on connection
     *
     *   The client should be given a number of IP addresses and ports where nodes are located. The client should
     *   select
     *   random nodes and should try to connect until an available one is found. On a successful connection the HELLO
     *   command should be used in order to retrieve the Node ID and other potentially useful information (server
     *   version, number of nodes).
     *
     * Here's how we implement that:
     *
     *   - $this->pool is initially just the list of connection details given
     *   - On ->connect(), we select one, and connect to it
     *   - On an unsuccessful connection, we select another node, until there are none left
     *   - On a successful connection, we use HELLO and get more information about the cluster, adding it to the pool
     *     (in case we need to switch connections later, etc.)
     *
     * The decision of which node to select is left to the injected strategy class (so we can cache unavailable node
     * information at some point - useful to work around shared-nothing)
     *
     * One invariant is that connect() should either throw an exception, or ->selected should contain a valid, connected
     * NodeConnectionInterface when it returns. (It is NOT guaranteed that
     */
    public function connect(): void
    {
        RETRY_CONNECT:

        if (empty($this->pool)) {
            throw new ClientException('The pool of connections is empty');
        }

        $this->selected = $this->strategy->pickInitialConnection($this->pool);

        try {
            $this->selected->connect();

            if ($this->autodiscover) {
                $this->discover();
            }
        } catch (BaseConnectionException $e) {
            $this->remove($this->selected);
            $this->selected = null;

            goto RETRY_CONNECT;
        }
    }

    public function disconnect()
    {
        if ($this->selected) {
            if ($this->selected->isConnected()) {
                $this->selected->disconnect();
            }

            $this->selected = null;
        }
    }

    public function executeCommand(CommandInterface $command)
    {
        return $this->retryCommandOnFailure($command, __FUNCTION__);
    }

    /**
     * @param CommandInterface $command
     * @return array
     */
    public function executeCommandOnCluster(CommandInterface $command)
    {
        $responses = [];

        foreach ($this->clusterNodes as $parameters) {
            $connection = $this->findOrCreateConnection($parameters);

            if (!$connection->isConnected()) {
                $connection->connect();
            }

            $responses[] = $connection->executeCommand($command);
        }

        return $responses;
    }

    /**
     * Executes the specified Redis command on all the current connections of the cluster
     *
     * @param CommandInterface $command A Redis command.
     * @return array
     */
    public function executeCommandOnPool(CommandInterface $command)
    {
        $responses = [];

        foreach ($this->pool as $connection) {
            // We are lazy about connecting to nodes other than the selected one
            if (!$connection->isConnected()) {
                $connection->connect();
            }

            $responses[] = $connection->executeCommand($command);
        }

        return $responses;
    }

    /**
     * Returns the current connection that we're using
     *
     * @param CommandInterface $_unused
     * @return null|NodeConnectionInterface
     */
    public function getConnection(CommandInterface $_unused = null): ?NodeConnectionInterface
    {
        if (!$this->selected) {
            $this->selected = $this->strategy->pickInitialConnection($this->pool);
        }

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
            $this->reset();
        }

        return $removed;
    }

    public function writeRequest(CommandInterface $command)
    {
        $this->retryCommandOnFailure($command, __FUNCTION__);
    }

    /**
     * Switches the internal connection instance in use.
     *
     * @param string|NodeConnectionInterface $connection Alias of a connection
     */
    public function switchTo($connection): void
    {
        if ($this->selected) {
            $this->disconnect();
        }
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
     * Calls HELLO on the connection and creates connections from the response
     *
     * The HELLO response returns a node priority and ID along with the details. The documentation says "lower is
     * better, means a node is more available" but doesn't give specifics for recommended client behaviour. This
     * client will not connect to nodes that have a priority higher than 99 (`CLUSTER LEAVING yes` marks a node as
     * priority 100).
     *
     * The other wrinkle is nodes known by more than one address. We'll favour the address given in the cluster
     * information, because it's more likely to be the publicly addressable one. For example, this happens if you
     * connect to a locally networked cluster via 127.0.0.1:7711 - it'll appear in the HELLO output as 10.1.1.100 or
     * whatever LAN address the local node has. Disque gives us the node ID, though, so we can match it up, and
     * replace the 127.0.0.1 in the connection parameters for the current node.
     *
     * The upshot is that you should make sure all addresses in the HELLO output are addressable and can be connected
     * to by every client (because any of them may be swapped to after a -LEAVING error response for example).
     *
     * @throws ClientException
     * @internal param NodeConnectionInterface $connection
     */
    public function discover(): void
    {
        if (!$this->isConnected()) {
            throw new ClientException('Client should be connected before calling discover');
        }

        $hello = new ServerHello();

        $response = $this->selected->executeCommand($hello);
        [$version, $id, $nodes] = $hello->parseResponse($response);

        if ($version !== ServerHello::VERSION_1) {
            throw new ClientException('HELLO protocol version ' . $version . ' not implemented');
        }

        $this->selectedId = $id;
        $this->clusterNodes = [];

        foreach ($nodes as $node) {
            $this->clusterNodes[$node[0]] = $node;
        }
    }

    /**
     * Reset the currently selected connection
     */
    protected function reset(): void
    {
        $this->selected = null;
        $this->selectedId = null;
    }

    private function findOrCreateConnection(Parameters $parameters): NodeConnectionInterface
    {
        foreach ($this->pool as $connection) {
            $p = $connection->getParameters();

            if ($p->host === $parameters->host && $p->port === $parameters->port) {
                return $connection;
            }
        }

        $connection = $this->connectionFactory->create($parameters);

        if (!$connection) {
            throw new ClientException('Cannot create conncetion to cluster node');
        }

        $this->add($connection);

        return $connection;
    }

    /**
     * Retries the execution of a command upon a LEAVING response
     *
     * Upstream documentation of how to handle LEAVING errors
     *
     *   The GETJOB command, or other commands, may return a -LEAVING error instead of blocking. This error should be
     *   considered by the client library as a request to connect to a different node, since the node it is connected
     *   to is not able to serve the request since it is leaving the cluster. Nodes in this state have a very high
     *   priority number published via HELLO, so will be unlikely to be picked at the next connection attempt.
     *
     * @param CommandInterface $command
     * @param string           $method
     * @return mixed
     */
    private function retryCommandOnFailure(CommandInterface $command, string $method)
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

            goto RETRY_COMMAND;
        }

        return $response;
    }
}
