<?php


namespace Predisque\Command;

class JobDeleteTest extends AbstractJobMultiTest
{
    /**
     * @group disconnected
     */
    public function testParseResponse()
    {
        $command = $this->getCommand();

        $this->assertSame(10, $command->parseResponse(10));
    }

    /**
     * @group connected
     */
    public function testDelete()
    {
        $disque = $this->getClient();

        $id = $disque->addJob('foo', 'bar', 10000);

        $job = $disque->show($id);
        $this->assertArrayHasKey('id', $job);

        $result = $disque->delJob($id);
        $this->assertEquals(1, $result);

        $job = $disque->show($id);
        $this->assertNull($job);

        $id1 = $disque->addJob('foo', 'bar', 10000);
        $id2 = $disque->addJob('foo', 'bar', 10000);

        $result = $disque->delJob($id1, $id2);
        $this->assertEquals(2, $result);
    }

    protected function getExpectedCommand()
    {
        return JobDelete::class;
    }

    protected function getExpectedId()
    {
        return 'DELJOB';
    }
}
