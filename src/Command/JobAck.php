<?php

namespace Predisque\Command;

use Predis\Command\Command;

class JobAck extends AbstractJobMulti
{
    public function getId()
    {
        return 'ACKJOB';
    }
}
