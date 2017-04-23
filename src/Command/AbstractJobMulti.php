<?php

namespace Predisque\Command;

use Predis\Command\Command;

abstract class AbstractJobMulti extends Command
{
    protected function filterArguments(array $arguments)
    {
        return self::normalizeArguments($arguments);
    }
}
