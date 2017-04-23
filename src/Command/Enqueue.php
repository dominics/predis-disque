<?php

namespace Varspool\Disque\Command;

use Predis\Command\Command;

class Enqueue extends Command
{
    public function getId()
    {
        return 'ENQUEUE';
    }
}
