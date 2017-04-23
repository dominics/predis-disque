<?php

namespace Predisque\Command;

use Predis\Command\Command;

class JobEnqueue extends Command
{
    public function getId()
    {
        return 'ENQUEUE';
    }
}
