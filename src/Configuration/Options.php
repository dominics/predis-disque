<?php

namespace Predisque\Configuration;

use Predis\Configuration\ExceptionsOption;
use Predis\Configuration\Options as PredisOptions;

class Options extends PredisOptions
{
    protected function getHandlers()
    {
        return [
            'connections' => ConnectionFactoryOption::class,
            'exceptions' => ExceptionsOption::class,
            'profile' => ProfileOption::class,
        ];
    }
}
