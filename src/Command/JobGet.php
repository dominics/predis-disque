<?php

namespace Predisque\Command;

use Predis\Command\Command;

class JobGet extends Command
{
    public function getId()
    {
        return 'GETJOB';
    }
}
