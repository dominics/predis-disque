<?php

namespace Varspool\Disque\Command;

use Predis\Command\Command;

class Dequeue extends Command
{
    public function getId()
    {
        return 'DEQUEUE';
    }
}
