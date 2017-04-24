<?php

namespace Predisque\Command;

class JobScanTest extends CommandTestCase
{
    /**
     * @group disconnected
     */
    public function testFilterArguments()
    {
        $arguments = [0, 'COUNT', 5];
        $expected = [0, 'COUNT', 5];

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
        $arguments = [['count' => 5, 'queue' => 'foo']];
        $expected = ['COUNT', 5, 'QUEUE', 'foo'];

        $command = $this->getCommand();
        $command->setArguments($arguments);

        $this->assertSame($expected, $command->getArguments());
    }

    /**
     * @group disconnected
     */
    public function testFilterArgumentsWithOptionsArrayAndCursor()
    {
        $arguments = [7, ['count' => 5]];
        $expected = [7, 'COUNT', 5];

        $command = $this->getCommand();
        $command->setArguments($arguments);

        $this->assertSame($expected, $command->getArguments());
    }

    /**
     * @group disconnected
     */
    public function testParseResponse()
    {
        $raw = ['3', ['key:1', 'key:2', 'key:3']];
        $expected = ['3', ['key:1', 'key:2', 'key:3']];

        $command = $this->getCommand();

        $this->assertSame($expected, $command->parseResponse($raw));
    }

    protected function getExpectedCommand()
    {
        return JobScan::class;
    }

    protected function getExpectedId()
    {
        return 'JSCAN';
    }
}
