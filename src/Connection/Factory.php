<?php

namespace Varspool\Disque\Connection;

use Predis\Connection\ParametersInterface;

class Factory extends \Predis\Connection\Factory
{
    private $defaults = array();

    /**
     * Assigns a default set of parameters applied to new connections.
     *
     * The set of parameters passed to create a new connection have precedence
     * over the default values set for the connection factory.
     *
     * @param array $parameters Set of connection parameters.
     */
    public function setDefaultParameters(array $parameters)
    {
        $this->defaults = $parameters;
    }

    /**
     * Returns the default set of parameters applied to new connections.
     *
     * @return array
     */
    public function getDefaultParameters()
    {
        return $this->defaults;
    }

    /**
     * Creates a connection parameters instance from the supplied argument.
     *
     * @param mixed $parameters Original connection parameters.
     *
     * @return ParametersInterface
     */
    protected function createParameters($parameters)
    {
        if (is_string($parameters)) {
            $parameters = Parameters::parse($parameters);
        } else {
            $parameters = $parameters ?: array();
        }

        if ($this->defaults) {
            $parameters += $this->defaults;
        }

        return new Parameters($parameters);
    }
}
