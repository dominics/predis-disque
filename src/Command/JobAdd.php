<?php

namespace Varspool\Disque\Command;

use Predis\Command\Command;

class JobAdd extends Command
{
    public function getId()
    {
        return 'ADDJOB';
    }
}
