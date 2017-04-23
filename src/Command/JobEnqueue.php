<?php

namespace Predisque\Command;

class JobEnqueue extends AbstractJobMulti
{
    public function getId()
    {
        return 'ENQUEUE';
    }
}
