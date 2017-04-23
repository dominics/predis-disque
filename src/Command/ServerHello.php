<?php

namespace Predisque\Command;

use Predis\Command\Command;

class ServerHello extends Command
{
    public function getId()
    {
        return 'HELLO';
    }
}
