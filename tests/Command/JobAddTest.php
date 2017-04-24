<?php

namespace Predisque\Command;

class JobAddTest extends CommandTestCase
{
    protected function getExpectedId()
    {
        return 'ADDJOB';
    }

    protected function getExpectedCommand()
    {
        return JobAdd::class;
    }

    /**
     * @group disconnected
     */
    public function testParseResponse()
    {
        $command = $this->getCommand();

        $id = 'D-12345678-123456789012345678-1234';

        $this->assertSame($id, $command->parseResponse($id));
    }

    /**
     * @group disconnected
     */
    public function testFilterArguments()
    {
        $arguments = ['foo', 'bar', 10000, 'REPLICATE', 2, 'DELAY', 2, 'RETRY', 60, 'TTL', 120, 'MAXLEN', 9999, 'ASYNC'];
        $expected = ['foo', 'bar', 10000, 'REPLICATE', 2, 'DELAY', 2, 'RETRY', 60, 'TTL', 120, 'MAXLEN', 9999, 'ASYNC'];

        $command = $this->getCommand();
        $command->setArguments($arguments);

        $this->assertSame($expected, $command->getArguments());
    }

    /**
     * @group disconnected
     */
    public function testFilterArgumentsWithOptionsArray()
    {
        $arguments = ['foo', 'bar', 10000, [
            'replicate' => 2,
            'delay' => 2,
            'retry' => 60,
            'ttl' => 120,
            'maxlen' => 9999,
            'async' => true,
        ]];
        $expected = ['foo', 'bar', 10000, 'REPLICATE', 2, 'DELAY', 2, 'RETRY', 60, 'TTL', 120, 'MAXLEN', 9999, 'ASYNC'];

        $command = $this->getCommand();
        $command->setArguments($arguments);

        $this->assertSame($expected, $command->getArguments());
    }
}
