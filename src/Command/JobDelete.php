<?php

namespace Predisque\Command;

use Predis\Command\Command;

class JobDelete extends Command
{
    public function getId()
    {
        return 'DELJOB';
    }
}
