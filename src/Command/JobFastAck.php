<?php

namespace Predisque\Command;

use Predis\Command\Command;

class JobFastAck extends Command
{
    public function getId()
    {
        return 'FASTACK';
    }
}
