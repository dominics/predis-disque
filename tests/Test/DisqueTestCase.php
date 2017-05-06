<?php

namespace Predisque\Test;

use PHPUnit\Framework\TestCase;
use Predis\Connection\NodeConnectionInterface;
use Predisque\Client;
use Predisque\Connection\ConnectionException;
use Predisque\Connection\Parameters;
use Predisque\Profile\Factory;
use Predisque\Profile\ProfileInterface;

/**
 * @see \PredisTestCase
 */
abstract class DisqueTestCase extends TestCase
{
    /**
     * Returns a new client instance.
     *
     * @param bool $flushall
     * @param null $parameters
     * @param null $options
     * @return Client
     */
    public function getClient($flushall = true, $parameters = null, $options = null)
    {
        $profile = $this->getProfile();

        if (method_exists($this, 'getExpectedId') && !$profile->supportsCommand($id = $this->getExpectedId())) {
            $this->markTestSkipped(
                "The profile {$profile->getVersion()} does not support command {$id}"
            );
        }

        $client = $this->createClient($parameters, $options, $flushall);

        return $client;
    }

    /**
     * Returns a named array with the default connection parameters and their values.
     *
     * @return array Default connection parameters.
     */
    protected function getDefaultParametersArray()
    {
        return [
            'scheme' => 'tcp',
            'host' => getenv('DISQUE_SERVER_HOST'),
            'port' => getenv('DISQUE_SERVER_PORT') ?: '12345' // Prevent accidents,
        ];
    }

    /**
     * Returns a named array with the default client options and their values.
     *
     * @return array Default connection parameters.
     */
    protected function getDefaultOptionsArray()
    {
        return [
            'profile' => getenv('DISQUE_SERVER_VERSION'),
        ];
    }

    /**
     * Returns a new instance of server profile.
     *
     * @param string $version Disque profile.
     * @return ProfileInterface
     * @throws \Predis\ClientException
     */
    protected function getProfile($version = null): ProfileInterface
    {
        return Factory::get($version ?: getenv('DISQUE_SERVER_VERSION'));
    }

    /**
     * Returns a new client instance.
     *
     * @param array $parameters Additional connection parameters.
     * @param array $options    Additional client options.
     * @param bool  $flushall   Flush all jobs, queues, stats before returning the client
     * @return Client
     * @throws \Predis\ClientException
     */
    protected function createClient(array $parameters = null, array $options = null, $flushall = true)
    {
        $parameters = array_merge(
            $this->getDefaultParametersArray(),
            $parameters ?: []
        );

        $options = array_merge(
            [
                'profile' => $this->getProfile(),
            ],
            $options ?: []
        );

        $client = new Client($parameters, $options);
        $client->connect();

        if ($flushall) {
            $client->debug('flushall');
        }

        return $client;
    }



    /**
     * @return NodeConnectionInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function getBadConnection(): NodeConnectionInterface
    {
        $connection = $this->createMock(NodeConnectionInterface::class);

        $connection->expects($this->any())
            ->method('connect')
            ->willThrowException(new ConnectionException($connection));

        return $connection;
    }

    /**
     * @return NodeConnectionInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function getGoodConnection(string $host = '127.0.0.1', int $port = 7711): NodeConnectionInterface
    {
        $connection = $this->createMock(NodeConnectionInterface::class);

        $connection->expects($this->any())
            ->method('getParameters')
            ->willReturn(new Parameters([
                'host' => $host,
                'port' => $port,
            ]));

        $connection->expects($this->any())
            ->method('isConnected')
            ->willReturn(true);

        return $connection;
    }

    protected function registerResponse($connection, $response, int $times = 1)
    {
        if (!isset($connection->responseIndex)) {
            $connection->responseIndex = 0;
        }

        for ($i = $times; $i > 0; $i--) {
            $connection->expects($this->at($connection->responseIndex++))
                ->method('executeCommand')
                ->willReturn($response);
        }
    }
}
