<?php

namespace Varspool\Disque\Command;

use Predis\Command\Command;

class QueueScan extends Command
{
    public function getId()
    {
        return 'QSCAN';
    }
}
