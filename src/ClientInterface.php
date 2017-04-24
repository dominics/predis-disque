<?php

namespace Predisque;

use Predis\Command\CommandInterface;
use Predis\Configuration\OptionsInterface;
use Predis\Connection\ConnectionInterface;
use Predisque\Profile\ProfileInterface;

/**
 * @method string addJob(string $queueName, string $job, int $msTimeout, array $options = [])
 * @method string delJob(string $jobId, string ...$jobIds)
 *
 * @method array show(string $jobId)
 * @method mixed debug($subcommand = null)
 * @method array info($section = null)
 */
interface ClientInterface /* extends \Predis\ClientInterface */
{
    /**
     * Returns the server profile used by the client.
     *
     * @return ProfileInterface
     */
    public function getProfile();

    /**
     * Returns the client options specified upon initialization.
     *
     * @return OptionsInterface
     */
    public function getOptions();

    /**
     * Opens the underlying connection to the server.
     */
    public function connect();

    /**
     * Closes the underlying connection from the server.
     */
    public function disconnect();

    /**
     * Returns the underlying connection instance.
     *
     * @return ConnectionInterface
     */
    public function getConnection();

    /**
     * Creates a new instance of the specified Redis command.
     *
     * @param string $method    Command ID.
     * @param array  $arguments Arguments for the command.
     *
     * @return CommandInterface
     */
    public function createCommand($method, $arguments = array());

    /**
     * Executes the specified Redis command.
     *
     * @param CommandInterface $command Command instance.
     *
     * @return mixed
     */
    public function executeCommand(CommandInterface $command);

    /**
     * Creates a Redis command with the specified arguments and sends a request
     * to the server.
     *
     * @param string $method    Command ID.
     * @param array  $arguments Arguments for the command.
     *
     * @return mixed
     */
    public function __call($method, $arguments);
}
