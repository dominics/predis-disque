<?php

namespace Predisque\Command;

use Predis\Command\Command;

class QueuePeek extends Command
{
    public function getId()
    {
        return 'QPEEK';
    }
}
