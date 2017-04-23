<?php

namespace Predisque\Command;

use Predis\Command\Command;

class JobWorking extends Command
{
    public function getId()
    {
        return 'WORKING';
    }
}
