<?php

namespace Predisque\Command;

class QueueStatisticsTest extends CommandTestCase
{
    /**
     * @group disconnected
     */
    public function testFilterArguments()
    {
        $arguments = ['queueName'];
        $expected = ['queueName'];

        $command = $this->getCommand();
        $command->setArguments($arguments);

        $this->assertSame($expected, $command->getArguments());
    }

    /**
     * @group disconnected
     */
    public function testParseResponse()
    {
        $raw = ['name', 'foo', 'len', 5340, 'age', 601];
        $expected = ['name' => 'foo', 'len' => 5340, 'age' => 601];

        $command = $this->getCommand();

        $this->assertSame($expected, $command->parseResponse($raw));
    }



    protected function getExpectedCommand()
    {
        return QueueStatistics::class;
    }

    /**
     * {@inheritdoc}
     */
    protected function getExpectedId()
    {
        return 'QSTAT';
    }
}
