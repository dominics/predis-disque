<?php

namespace Predisque\Command;

use Predis\Client;
use Predis\Command\CommandInterface;
use Predis\Command\PrefixableCommandInterface;
use Predisque\Test\DisqueTestCase;

abstract class CommandTestCase extends DisqueTestCase
{


    /**
     * Returns the expected command ID.
     *
     * @return string
     */
    abstract protected function getExpectedId();

    /**
     * @group disconnected
     */
    public function testCommandId()
    {
        $command = $this->getCommand();

        $this->assertInstanceOf('Predis\Command\CommandInterface', $command);
        $this->assertEquals($this->getExpectedId(), $command->getId());
    }

    /**
     * Returns a new command instance.
     *
     * @return CommandInterface
     */
    public function getCommand()
    {
        $command = $this->getExpectedCommand();

        return $command instanceof CommandInterface ? $command : new $command();
    }

    /**
     * Returns the expected command.
     *
     * @return CommandInterface|string Instance or FQN of the expected command.
     */
    abstract protected function getExpectedCommand();

    /**
     * @group disconnected
     */
    public function testRawArguments()
    {
        $expected = ['1st', '2nd', '3rd', '4th'];

        $command = $this->getCommand();
        $command->setRawArguments($expected);

        $this->assertSame($expected, $command->getArguments());
    }

    /**
     * Returns wether the command is prefixable or not.
     *
     * @return bool
     */
    protected function isPrefixable()
    {
        return $this->getCommand() instanceof PrefixableCommandInterface;
    }

    /**
     * Returns a new command instance with the specified arguments.
     *
     * @param ... List of arguments for the command.
     *
     * @return CommandInterface
     */
    protected function getCommandWithArguments(/* arguments */)
    {
        return $this->getCommandWithArgumentsArray(func_get_args());
    }

    /**
     * Returns a new command instance with the specified arguments.
     *
     * @param array $arguments Arguments for the command.
     *
     * @return CommandInterface
     */
    protected function getCommandWithArgumentsArray(array $arguments)
    {
        $command = $this->getCommand();
        $command->setArguments($arguments);

        return $command;
    }
}
