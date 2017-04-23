<?php

namespace Varspool\Disque\Profile;

use Varspool\Disque\Command\ServerDebug;
use Varspool\Disque\Command\ServerInfo;

class DisqueVersion100 extends DisqueProfile
{
    public function getVersion()
    {
        return '1.0';
    }

    protected function getSupportedCommands()
    {
        return [
            'INFO' => ServerInfo::class,
            'DEBUG' => ServerDebug::class,
        ];
    }
}
