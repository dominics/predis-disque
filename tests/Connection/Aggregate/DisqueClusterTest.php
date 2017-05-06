<?php

namespace Predisque\Connection\Aggregate;

use Predisque\Connection\Factory;
use Predisque\Test\DisqueTestCase;

class DisqueClusterTest extends DisqueTestCase
{
    /**
     * @var DisqueCluster
     */
    private $cluster;

    /**
     * @var Factory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $factory;

    public function setUp()
    {
        parent::setUp();

        $this->factory = $this->createMock(Factory::class);

        $this->cluster = new DisqueCluster(
            $this->factory
        );
    }

    /**
     * @expectedException \Predisque\ClientException
     * @expectedExceptionMessage The pool of connections is empty
     * @group                    disconnected
     */
    public function testConnectNoConnections()
    {
        $this->cluster->connect();
    }

    /**
     * @group connected
     */
    public function testConnect()
    {
        $parameters = ['tcp://127.0.0.1', 'tcp://127.0.0.1:7712'];
        $options = ['cluster' => true];

        // getClient implicitly does a connect to run the flushall
        $client = $this->getClient(true, $parameters, $options);

        $connection = $client->getConnection();

        $this->assertInstanceOf(DisqueCluster::class, $connection);
    }
}
