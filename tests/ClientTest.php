<?php

namespace Predisque;

use Predis\Connection\NodeConnectionInterface;
use Predis\Connection\StreamConnection;
use Predisque\Connection\Aggregate\DisqueCluster;
use Predisque\Profile\Factory;
use Predisque\Test\DisqueTestCase;

/**
 * @see \Predis\ClientTest
 */
class ClientTest extends DisqueTestCase
{
    public function defaultClientConstructorArguments(): iterable
    {
        yield [null, null, 'no arguments'];

        yield [[
            'host' => '127.0.0.1',
            'port' => 7711,
        ], null, 'detail array'];

        yield ['tcp://127.0.0.1:7711', null, 'single string'];

        yield [['tcp://127.0.0.1:7711'], null, 'single string in array'];

        yield [[
            'tcp://127.0.0.1:7711',
            'tcp://127.0.0.1:7712',
            'tcp://127.0.0.1:7713',
        ], null, 'multiple strings'];
    }

    /**
     * @dataProvider defaultClientConstructorArguments
     * @group disconnected
     */
    public function testConstructorDefaultDisconnected($parameters, $options, string $case)
    {
        $client = new Client($parameters, $options);

        /**
         * @var DisqueCluster $connection
         */
        $connection = $client->getConnection();

        $this->assertInstanceOf(DisqueCluster::class, $client->getConnection(), $case . ' should produce a DisqueCluster');
        $this->assertFalse($client->isConnected(), $case . ' should not cause connection immediately');

        $current = $connection->getConnectionById(0);
        $this->assertInstanceOf(StreamConnection::class, $current);

        $parameters = $current->getParameters();
        $this->assertSame($parameters->host, '127.0.0.1', $case . ' host is as expected');
        $this->assertSame($parameters->port, 7711, $case . ' port is as expected');

        $options = $client->getOptions();
        $this->assertSame($options->profile->getVersion(), Factory::getDefault()->getVersion(), $case . ' uses expected profile');
    }

    /**
     * @dataProvider defaultClientConstructorArguments
     * @group connected
     */
    public function testConstructorDefaultConnected($parameters, $options, string $case)
    {
        $client = new Client($parameters, $options);
        $client->connect();

        $this->assertTrue($client->isConnected(), $case . ' should say client is connected after ->connect()');
    }
}
