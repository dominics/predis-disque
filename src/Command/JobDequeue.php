<?php

namespace Predisque\Command;

class JobDequeue extends AbstractJobMulti
{
    public function getId()
    {
        return 'DEQUEUE';
    }
}
