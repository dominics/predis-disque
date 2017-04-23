<?php

namespace Predisque\Command;

class JobShow extends AbstractJob
{
    public function getId()
    {
        return 'SHOW';
    }
}
