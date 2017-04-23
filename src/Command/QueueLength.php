<?php

namespace Predisque\Command;

class QueueLength extends Command
{
    public function getId()
    {
        return 'QLEN';
    }
}
