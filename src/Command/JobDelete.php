<?php

namespace Predisque\Command;

class JobDelete extends AbstractJobMulti
{
    public function getId()
    {
        return 'DELJOB';
    }
}
