<?php

namespace Varspool\Disque\Profile;

class DisqueVersion100 extends DisqueProfile
{
    public function getVersion()
    {
        return '1.0';
    }

    /**
     * Returns a map of all the commands supported by the profile and their
     * actual PHP classes.
     *
     * @return array
     */
    protected function getSupportedCommands()
    {
        return [
            'INFO' => ServerInfo::class,
        ];
    }
}
