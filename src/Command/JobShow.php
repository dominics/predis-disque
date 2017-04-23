<?php

namespace Varspool\Disque\Command;

use Predis\Command\Command;

class JobShow extends Command
{
    public function getId()
    {
        return 'SHOW';
    }
}
