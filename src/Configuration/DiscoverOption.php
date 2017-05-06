<?php

namespace Predisque\Configuration;

use Predis\Configuration\OptionInterface;
use Predis\Configuration\OptionsInterface;
use Predisque\Connection\Aggregate\ClusterInterface;
use Predisque\Connection\Aggregate\DisqueCluster;

class DiscoverOption implements OptionInterface
{
    public function filter(OptionsInterface $options, $value)
    {
        return (bool)$value;
    }

    public function getDefault(OptionsInterface $options)
    {
        return true;
    }
}
