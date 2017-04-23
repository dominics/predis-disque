<?php

namespace Predisque\Command;

use Predis\Command\Command;

class JobScan extends Command
{
    public function getId()
    {
        return 'JSCAN';
    }
}
