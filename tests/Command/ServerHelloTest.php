<?php

namespace Predisque\Command;

class ServerHelloTest extends CommandTestCase
{
    /**
     * @group disconnected
     */
    public function testFilterArguments()
    {
        $arguments = ['FLUSHALL'];
        $expected = ['FLUSHALL'];

        $command = $this->getCommand();
        $command->setArguments($arguments);

        $this->assertSame($expected, $command->getArguments());
    }

    /**
     * @group disconnected
     */
    public function testParseResponseOfConfigSet()
    {
        $this->assertSame('OK', $this->getCommand()->parseResponse('OK'));
    }

    /**
     * @group disconnected
     */
    public function testParseResponseOfConfigResetstat()
    {
        $this->assertSame('OK', $this->getCommand()->parseResponse('OK'));
    }

    /**
     * @group connected
     */
    public function testHello()
    {
        $response = $this->getClient()->hello();

        $this->assertCount(3, $response);

        [$format, $nodeId, $nodes] = $response;

        $this->assertInternalType('int', $format);
        $this->assertRegExp('/[a-f0-9]{32}/', $nodeId);
        $this->assertInternalType('array', $nodes);
        $this->assertGreaterThanOrEqual(1, count($nodes), 'At least one node returned');
        $this->assertInternalType('array', $nodes[0]);
        $this->assertRegExp('/[a-f0-9]{32}/', $nodes[0][0]);
    }

    protected function getExpectedId()
    {
        return 'HELLO';
    }

    protected function getExpectedCommand()
    {
        return ServerHello::class;
    }
}
