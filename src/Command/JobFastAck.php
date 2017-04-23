<?php

namespace Predisque\Command;

use Predis\Command\Command;

class JobFastAck extends AbstractJobMulti
{
    public function getId()
    {
        return 'FASTACK';
    }
}
