<?php

namespace Predisque\Command;

class JobFastAck extends Command
{
    public function getId()
    {
        return 'FASTACK';
    }

    protected function filterArguments(array $arguments)
    {
        return self::normalizeArguments($arguments);
    }
}
