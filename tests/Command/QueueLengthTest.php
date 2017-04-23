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
 * @group realm-hash
 */
class QueueLengthTest extends CommandTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function getExpectedCommand()
    {
        return QueueLength::class;
    }

    /**
     * {@inheritdoc}
     */
    protected function getExpectedId()
    {
        return 'QLEN';
    }

    /**
     * @group disconnected
     */
    public function testFilterArguments()
    {
        $arguments = array('key');
        $expected = array('key');

        $command = $this->getCommand();
        $command->setArguments($arguments);

        $this->assertSame($expected, $command->getArguments());
    }

    /**
     * @group disconnected
     */
    public function testParseResponse()
    {
        $this->assertSame(1, $this->getCommand()->parseResponse(1));
    }

    /**
     * @group connected
     */
    public function testReturnsLengthOfQueue()
    {
        $disque = $this->getClient();

        $disque->addjob('foo', 'bar', 1000);
        $disque->addjob('foo', 'bar2', 1000);

        $this->assertSame(2, $disque->qlen('foo'));
        $this->assertSame(0, $disque->qlen('unknown'));
    }
}
