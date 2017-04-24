<?php

namespace Predisque\Command;

class JobEnqueue extends Command
{
    public function getId()
    {
        return 'ENQUEUE';
    }

    protected function filterArguments(array $arguments)
    {
        return self::normalizeArguments($arguments);
    }
}
