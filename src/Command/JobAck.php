<?php

namespace Predisque\Command;

class JobAck extends Command
{
    public function getId()
    {
        return 'ACKJOB';
    }

    protected function filterArguments(array $arguments)
    {
        return self::normalizeArguments($arguments);
    }
}
