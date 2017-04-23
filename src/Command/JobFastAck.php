<?php

namespace Predisque\Command;

class JobFastAck extends AbstractJobMulti
{
    public function getId()
    {
        return 'FASTACK';
    }
}
