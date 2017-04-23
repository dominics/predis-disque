<?php

namespace Predisque\Command;

class JobWorking extends AbstractJob
{
    public function getId()
    {
        return 'WORKING';
    }
}
