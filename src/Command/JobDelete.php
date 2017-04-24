<?php

namespace Predisque\Command;

class JobDelete extends Command
{
    public function getId()
    {
        return 'DELJOB';
    }

    protected function filterArguments(array $arguments)
    {
        return self::normalizeArguments($arguments);
    }
}
