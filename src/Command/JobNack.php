<?php

namespace Predisque\Command;

use Predis\Command\Command;

class JobNack extends Command
{
    public function getId()
    {
        return 'NACK';
    }
}
