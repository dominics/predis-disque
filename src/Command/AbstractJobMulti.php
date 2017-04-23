<?php

namespace Predisque\Command;

abstract class AbstractJobMulti extends Command
{
    protected function filterArguments(array $arguments)
    {
        return self::normalizeArguments($arguments);
    }
}
