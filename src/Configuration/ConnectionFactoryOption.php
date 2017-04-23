<?php

namespace Varspool\Disque\Configuration;

use Predis\Configuration\ConnectionFactoryOption as PredisConnectionFactoryOption;
use Predis\Configuration\OptionsInterface;
use Varspool\Disque\Connection\Factory;

class ConnectionFactoryOption extends PredisConnectionFactoryOption
{
    public function getDefault(OptionsInterface $options)
    {
        $factory = new Factory();

        if ($options->defined('parameters')) {
            $factory->setDefaultParameters($options->parameters);
        }

        return $factory;
    }
}
