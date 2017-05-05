<?php

namespace Predisque\Configuration;

use Predis\Configuration\OptionInterface;
use Predis\Configuration\OptionsInterface;
use Predisque\Connection\Aggregate\ClusterInterface;
use Predisque\Connection\Aggregate\DisqueCluster;

class ClusterOption implements OptionInterface
{
    public function filter(OptionsInterface $options, $value)
    {
        if (!$value instanceof ClusterInterface) {
            throw new \InvalidArgumentException(
                "An instance of type 'Predisque\\Connection\\Aggregate\\ClusterInterface' was expected."
            );
        }

        return $value;
    }

    public function getDefault(OptionsInterface $options)
    {
        return new DisqueCluster();
    }
}
