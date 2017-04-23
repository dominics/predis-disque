<?php

namespace Predisque\Command;

class JobNack extends Command
{
    public function getId()
    {
        return 'NACK';
    }
}
