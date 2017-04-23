<?php

namespace Predisque\Command;

class JobAck extends AbstractJobMulti
{
    public function getId()
    {
        return 'ACKJOB';
    }
}
