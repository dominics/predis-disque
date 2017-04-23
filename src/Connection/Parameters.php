<?php

namespace Predisque\Connection;

use Predis\Connection\Parameters as PredisParameters;

class Parameters extends PredisParameters
{
    private static $defaults = array(
        'scheme' => 'tcp',
        'host' => '127.0.0.1',
        'port' => 7711,
    );

    /**
     * Returns some default parameters with their values.
     *
     * @return array
     */
    protected function getDefaults()
    {
        return self::$defaults;
    }
}
