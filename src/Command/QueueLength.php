<?php

namespace Varspool\Disque\Command;

use Predis\Command\Command;

class QueueLength extends Command
{
    public function getId()
    {
        return 'QLEN';
    }
}
