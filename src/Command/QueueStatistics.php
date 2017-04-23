<?php

namespace Predisque\Command;

use Predis\Command\Command;

class QueueStatistics extends Command
{
    public function getId()
    {
        return 'QSTAT';
    }
}
