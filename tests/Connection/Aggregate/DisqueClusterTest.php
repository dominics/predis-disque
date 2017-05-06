<?php

namespace Predisque\Connection\Aggregate;

use Predis\Connection\ConnectionException;
use Predis\Connection\NodeConnectionInterface;
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
            $this->factory,
            false
        );
    }

    /**
     * @expectedException        \Predisque\ClientException
     * @expectedExceptionMessage The pool of connections is empty
     * @group                    disconnected
     */
    public function testConnectNoConnections()
    {
        $this->cluster->connect();
    }

    /**
     * @group disconnected
     */
    public function testConnectRetry()
    {
        $bad1 = $this->getBadConnection();
        $bad2 = $this->getBadConnection();
        $bad3 = $this->getBadConnection();
        $bad4 = $this->getBadConnection();
        $good = $this->getGoodConnection();

        $this->cluster->add($bad1);
        $this->cluster->add($bad2);
        $this->cluster->add($good);
        $this->cluster->add($bad3);
        $this->cluster->add($bad4);

        $this->cluster->connect();

        $inner = $this->cluster->getConnection();

        $this->assertEquals($inner, $good);
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
