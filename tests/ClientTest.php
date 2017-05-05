<?php

namespace Predisque;

use Predis\Connection\NodeConnectionInterface;
use Predisque\Profile\Factory;
use Predisque\Test\DisqueTestCase;

/**
 * @see \Predis\ClientTest
 */
class ClientTest extends DisqueTestCase
{
    /**
     * @group disconnected
     */
    public function testConstructorWithoutArguments()
    {
        $this->assertClientDefaults($client = new Client());
    }

    protected function assertClientDefaults(Client $client)
    {
        $connection = $client->getConnection();
        $this->assertInstanceOf(NodeConnectionInterface::class, $connection);

        $parameters = $connection->getParameters();
        $this->assertSame($parameters->host, '127.0.0.1');
        $this->assertSame($parameters->port, 7711);

        $options = $client->getOptions();
        $this->assertSame($options->profile->getVersion(), Factory::getDefault()->getVersion());

        $this->assertFalse($client->isConnected());
    }

    /**
     * @group disconnected
     */
    public function testConstructorSingleString()
    {
        $this->assertClientDefaults($client = new Client('tcp://127.0.0.1:7711'));
    }

    /**
     * @group disconnected
     */
    public function testConstructorMultipleString()
    {
        $this->assertClientDefaults($client = new Client([
            'tcp://127.0.0.1:7711',
            'tcp://127.0.0.1:7712',
            'tcp://127.0.0.1:7713',
        ]));
    }

    /**
     * @group disconnected
     */
    public function testConstructorArray()
    {
        $this->assertClientDefaults($client = new Client([
            'host' => '127.0.0.1',
            'port' => 7711,
        ]));
    }

    /**
     * @group connected
     */
    public function testConstructorArrayConnect()
    {
        $client = new Client([
            'host' => '127.0.0.1',
            'port' => 7711,
        ]);

        $client->connect();
    }

    /**
     * @group connected
     */
    public function testConstructorMultipleStringConnect()
    {
        $client = new Client([
            'tcp://127.0.0.1:7711',
            'tcp://127.0.0.1:7712',
            'tcp://127.0.0.1:7713',
        ]);

        $client->connect();
    }

    /**
     * @group connected
     */
    public function testConstructorSingleStringConnect()
    {
        $client = new Client('tcp://127.0.0.1:7711');
        $client->connect();
    }
}
