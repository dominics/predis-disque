<?php

namespace Predisque;

use Predis\Connection\NodeConnectionInterface;
use Predis\Connection\StreamConnection;
use Predisque\Connection\Aggregate\DisqueCluster;
use Predisque\Connection\ConnectionException;
use Predisque\Connection\Factory as ConnectionFactory;
use Predisque\Connection\Parameters;
use Predisque\Profile\Factory as ProfileFactory;
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
        $this->assertSame($options->profile->getVersion(), ProfileFactory::getDefault()->getVersion(), $case . ' uses expected profile');
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

    /**
     * In this test, we have:
     *   - A mocked HELLO response with three active nodes (abc, def, ghi) on localhost
     *   - A single mocked connection configured on the client (connected to def)
     *   - The client makes three INFO calls, and we've set the connection to fail (with a ConnectionException) on
     *     the second one
     *
     * So, we'd expect that once it does fail, the connection factory will be consulted (because we'll want to retry
     * the command on either abc or ghi)
     *
     * @group disconnected
     */
    public function testRetryOnNodeLeaving()
    {
        $factory = $this->createMock(ConnectionFactory::class);

        $factory->expects($this->exactly(1))
            ->method('create')
            ->with($this->callback(function ($params) {
                $this->assertInstanceOf(Parameters::class, $params);
                $this->assertEquals('127.0.0.1', $params->host);
                $this->assertThat($params->port, $this->logicalOr($this->equalTo(7711), $this->equalTo(7713)));
                return true;
            }))
            ->willReturnCallback(function ($p) {
                $works = $this->getGoodConnection($p->host, $p->port);

                $this->registerResponse($works, "# foo\nbar: baz", 2);

                return $works;
            });

        $connection = $this->getGoodConnection('127.0.0.1', 7712);

        $this->registerResponse(
            $connection,
            [1, 'abc', ['abc', '127.0.0.1', 7711, 1], ['def', '127.0.0.1', 7712, 1], ['ghi', '127.0.0.1', 7713, 1]],
            4
        );

        $this->registerResponse($connection, "# foo\nbar: baz", 1);

        $connection->expects($this->at($connection->responseIndex++))
            ->method('executeCommand')
            ->willThrowException(new ConnectionException($connection, 'foo bar hahahahahahaha!'));

        $cluster = new DisqueCluster($factory);
        $cluster->add($connection);

        $client = new Client($cluster);
        $client->connect();

        $info = $client->info();
        $info = $client->info(); // We've set this one up to fail!
        $info = $client->info();
    }
}
