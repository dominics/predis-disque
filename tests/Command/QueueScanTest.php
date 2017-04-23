<?php

/*
 * This file is part of the Predis package.
 *
 * (c) Daniele Alessandri <suppakilla@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Predisque\Command;

/**
 * @group commands
 * @group realm-key
 */
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

    /**
     * {@inheritdoc}
     */
    protected function getExpectedId()
    {
        return 'QSCAN';
    }
}
