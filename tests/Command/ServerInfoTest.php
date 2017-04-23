<?php

namespace Predisque\Command;

/**
 * @see \Predis\Command\ServerInfoV26xTest
 */
class ServerInfoTest extends CommandTestCase
{
    /**
     * @group disconnected
     */
    public function testFilterArguments()
    {
        $command = $this->getCommand();
        $command->setArguments([]);

        $this->assertSame([], $command->getArguments());
    }

    /**
     * @group disconnected
     */
    public function testDoesNotEmitPhpNoticeOnEmptyResponse()
    {
        $this->assertSame([], $this->getCommand()->parseResponse(''));
    }

    /**
     * @group connected
     */
    public function testReturnsAnArrayOfInfo()
    {
        $redis = $this->getClient();
        $command = $this->getCommand();

        $this->assertInternalType('array', $info = $redis->executeCommand($command));
        $this->assertArrayHasKey('disque_version', isset($info['Server']) ? $info['Server'] : $info);
    }

    protected function getExpectedId()
    {
        return 'INFO';
    }

    protected function getExpectedCommand()
    {
        return ServerInfo::class;
    }
}
