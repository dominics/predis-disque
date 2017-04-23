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
class JobScanTest extends CommandTestCase
{
    protected function getExpectedCommand()
    {
        return JobScan::class;
    }

    /**
     * {@inheritdoc}
     */
    protected function getExpectedId()
    {
        return 'JSCAN';
    }

    /**
     * @group disconnected
     */
    public function testFilterArguments()
    {
        $arguments = array(0, 'MATCH', 'key:*', 'COUNT', 5);
        $expected = array(0, 'MATCH', 'key:*', 'COUNT', 5);

        $command = $this->getCommand();
        $command->setArguments($arguments);

        $this->assertSame($expected, $command->getArguments());
    }

    /**
     * @group disconnected
     */
    public function testFilterArgumentsBasicUsage()
    {
        $arguments = array(0);
        $expected = array(0);

        $command = $this->getCommand();
        $command->setArguments($arguments);

        $this->assertSame($expected, $command->getArguments());
    }

    /**
     * @group disconnected
     */
    public function testFilterArgumentsWithOptionsArray()
    {
        $arguments = array(0, array('match' => 'key:*', 'count' => 5));
        $expected = array(0, 'MATCH', 'key:*', 'COUNT', 5);

        $command = $this->getCommand();
        $command->setArguments($arguments);

        $this->assertSame($expected, $command->getArguments());
    }

    /**
     * @group disconnected
     */
    public function testParseResponse()
    {
        $raw = array('3', array('key:1', 'key:2', 'key:3'));
        $expected = array('3', array('key:1', 'key:2', 'key:3'));

        $command = $this->getCommand();

        $this->assertSame($expected, $command->parseResponse($raw));
    }
}
