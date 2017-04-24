<?php

namespace Predisque\Command;

use Predisque\Job;

class JobGetTest extends CommandTestCase
{
    protected function getExpectedId()
    {
        return 'GETJOB';
    }

    protected function getExpectedCommand()
    {
        return JobGet::class;
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
        $arguments = ['NOHANG', 'TIMEOUT', 100, 'FROM', 'foo', 'bar'];
        $expected = ['NOHANG', 'TIMEOUT', 100, 'FROM', 'foo', 'bar'];

        $command = $this->getCommand();
        $command->setArguments($arguments);

        $this->assertSame($expected, $command->getArguments());
    }

    /**
     * @group disconnected
     */
    public function testFilterArgumentsWithOptionsArray()
    {
        $arguments = ['foo', 'bar', [
            'nohang' => true,
            'count' => 7,
            'withcounters' => true,
        ]];
        $expected = ['NOHANG', 'COUNT', 7, 'WITHCOUNTERS', 'FROM', 'foo', 'bar'];

        $command = $this->getCommand();
        $command->setArguments($arguments);

        $this->assertSame($expected, $command->getArguments());
    }

    /**
     * @group connected
     */
    public function testGetSingleJobWithQueuesArray()
    {
        $disque = $this->getClient();

        $id = $disque->addJob('foo', 'bar', 10000);

        $jobs = $disque->getJob(['foo'], ['nohang' => true]);

        $this->assertInternalType('array', $jobs);
        $this->assertCount(1, $jobs);

        $job = $jobs[0];

        $this->assertInstanceOf(Job::class, $job);
        $this->assertEquals($id, $job->id);
    }

    /**
     * @group connected
     */
    public function testGetMultipleJobsWithQueuesArray()
    {
        $disque = $this->getClient();

        $id1 = $disque->addJob('foo', 'bar1', 10000);
        $id2 = $disque->addJob('foo', 'bar2', 10000);
        $id3 = $disque->addJob('foo', 'bar3', 10000);
        $id4 = $disque->addJob('foo', 'bar4', 10000);

        $jobs = $disque->getJob(['foo'], ['nohang' => true, 'count' => 3]);

        $this->assertInternalType('array', $jobs);
        $this->assertCount(3, $jobs);

        foreach ($jobs as $job) {
            $this->assertInstanceOf(Job::class, $job);
            $this->assertTrue($job->id === $id1 || $job->id === $id2 || $job->id === $id3, 'Job is one of first 3 in queue');
            $this->assertFalse($job->id === $id4, 'Job is fourth in queue');
        }
    }
}
