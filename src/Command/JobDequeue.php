<?php

namespace Predisque\Command;

class JobDequeue extends Command
{
    public function getId()
    {
        return 'DEQUEUE';
    }

    protected function filterArguments(array $arguments)
    {
        return self::normalizeArguments($arguments);
    }
}
