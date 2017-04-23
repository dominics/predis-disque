<?php

namespace Predisque\Command;

use Predis\Command\Command;

class JobAck extends Command
{
    public function getId()
    {
        return 'ACKJOB';
    }
}
