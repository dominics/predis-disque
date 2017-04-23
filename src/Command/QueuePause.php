<?php

namespace Predisque\Command;

use Predis\Command\Command;

class QueuePause extends Command
{
    public function getId()
    {
        return 'PAUSE';
    }
}
