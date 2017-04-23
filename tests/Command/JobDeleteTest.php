<?php


namespace Predisque\Command;

/**
 * @group commands
 * @group realm-key
 */
class JobDeleteTest extends CommandTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function getExpectedCommand()
    {
        return JobDelete::class;
    }

    /**
     * {@inheritdoc}
     */
    protected function getExpectedId()
    {
        return 'DELJOB';
    }

    /**
     * @group disconnected
     */
    public function testFilterArguments()
    {
        $arguments = array('key1', 'key2', 'key3');
        $expected = array('key1', 'key2', 'key3');

        $command = $this->getCommand();
        $command->setArguments($arguments);
        $this->assertSame($expected, $command->getArguments());
    }

    /**
     * @group disconnected
     */
    public function testFilterArgumentsAsSingleArray()
    {
        $arguments = array(array('key1', 'key2', 'key3'));
        $expected = array('key1', 'key2', 'key3');

        $command = $this->getCommand();
        $command->setArguments($arguments);

        $this->assertSame($expected, $command->getArguments());
    }

    /**
     * @group disconnected
     */
    public function testParseResponse()
    {
        $command = $this->getCommand();

        $this->assertSame(10, $command->parseResponse(10));
    }
}
