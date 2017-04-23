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
        $client = new Client();

        $connection = $client->getConnection();
        $this->assertInstanceOf(NodeConnectionInterface::class, $connection);

        $parameters = $connection->getParameters();
        $this->assertSame($parameters->host, '127.0.0.1');
        $this->assertSame($parameters->port, 7711);

        $options = $client->getOptions();
        $this->assertSame($options->profile->getVersion(), Factory::getDefault()->getVersion());

        $this->assertFalse($client->isConnected());
    }
}
