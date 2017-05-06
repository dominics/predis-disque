<?php

namespace Predisque\Connection;

use Predis\Connection\Parameters as PredisParameters;

class Parameters extends PredisParameters
{
    protected $parameters;

    /**
     * @param array $parameters Named array of connection parameters.
     */
    public function __construct(array $parameters = [])
    {
        parent::__construct($parameters);

        $this->parameters = $this->filter($parameters) + $this->getDefaults();
    }

    public function __get($parameter)
    {
        if (isset($this->parameters[$parameter])) {
            return $this->parameters[$parameter];
        }
    }

    public function __isset($parameter)
    {
        return isset($this->parameters[$parameter]);
    }

    public function __sleep()
    {
        return ['parameters'];
    }

    protected function getDefaults()
    {
        return [
            'scheme' => 'tcp',
            'host' => '127.0.0.1',
            'port' => 7711,
        ];
    }

    public function toArray()
    {
        return $this->parameters;
    }
}
