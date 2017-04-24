<?php

namespace Predisque\Command;

abstract class AbstractJobMultiTest extends CommandTestCase
{
    /**
     * @group disconnected
     */
    public function testFilterArguments()
    {
        $arguments = ['key1', 'key2', 'key3'];
        $expected = ['key1', 'key2', 'key3'];

        $command = $this->getCommand();
        $command->setArguments($arguments);
        $this->assertSame($expected, $command->getArguments());
    }

    /**
     * @group disconnected
     */
    public function testFilterArgumentsAsSingleArray()
    {
        $arguments = [['key1', 'key2', 'key3']];
        $expected = ['key1', 'key2', 'key3'];

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
