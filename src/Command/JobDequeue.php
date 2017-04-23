<?php

namespace Predisque\Command;

use Predis\Command\Command;

class JobDequeue extends Command
{
    public function getId()
    {
        return 'DEQUEUE';
    }
}
