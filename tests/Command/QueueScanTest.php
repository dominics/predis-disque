<?php

namespace Predisque\Command;

class QueueScanTest extends CommandTestCase
{
    /**
     * @group disconnected
     */
    public function testFilterArguments()
    {
        $arguments = [0, 'MATCH', 'key:*', 'COUNT', 5];
        $expected = [0, 'MATCH', 'key:*', 'COUNT', 5];

        $command = $this->getCommand();
        $command->setArguments($arguments);

        $this->assertSame($expected, $command->getArguments());
    }

    /**
     * @group disconnected
     */
    public function testFilterArgumentsBasicUsage()
    {
        $arguments = [0];
        $expected = [0];

        $command = $this->getCommand();
        $command->setArguments($arguments);

        $this->assertSame($expected, $command->getArguments());
    }

    /**
     * @group disconnected
     */
    public function testFilterArgumentsWithOptionsArray()
    {
        $arguments = [['count' => 5, 'minlen' => 5]];
        $expected = ['COUNT', 5, 'MINLEN', 5];

        $command = $this->getCommand();
        $command->setArguments($arguments);

        $this->assertSame($expected, $command->getArguments());
    }

    /**
     * @group disconnected
     */
    public function testFilterArgumentsWithOptionsArrayAndCursor()
    {
        $arguments = [7, ['count' => 5, 'minlen' => 5]];
        $expected = [7, 'COUNT', 5, 'MINLEN', 5];

        $command = $this->getCommand();
        $command->setArguments($arguments);

        $this->assertSame($expected, $command->getArguments());
    }

    /**
     * @group disconnected
     */
    public function testParseResponse()
    {
        $raw = [['key:1', 'key:2', 'key:3']];
        $expected = [['key:1', 'key:2', 'key:3']];

        $command = $this->getCommand();

        $this->assertSame($expected, $command->parseResponse($raw));
    }

    protected function getExpectedCommand()
    {
        return QueueScan::class;
    }

    protected function getExpectedId()
    {
        return 'QSCAN';
    }
}
