<?php

namespace Varspool\Disque\Profile;

use Varspool\Disque\Command\JobAck;
use Varspool\Disque\Command\JobAdd;
use Varspool\Disque\Command\JobDelete;
use Varspool\Disque\Command\JobFastAck;
use Varspool\Disque\Command\JobGet;
use Varspool\Disque\Command\JobNack;
use Varspool\Disque\Command\JobScan;
use Varspool\Disque\Command\JobShow;
use Varspool\Disque\Command\JobWorking;
use Varspool\Disque\Command\QueueLength;
use Varspool\Disque\Command\QueuePause;
use Varspool\Disque\Command\QueuePeek;
use Varspool\Disque\Command\QueueScan;
use Varspool\Disque\Command\QueueStatistics;
use Varspool\Disque\Command\ServerConfig;
use Varspool\Disque\Command\ServerDebug;
use Varspool\Disque\Command\ServerHello;
use Varspool\Disque\Command\ServerInfo;

class DisqueVersion100 extends DisqueProfile
{
    public function getVersion()
    {
        return '1.0';
    }

    /**
     * @return array
     */
    protected function getSupportedCommands()
    {
        return [
            'ACKJOB' => JobAck::class,
            'ADDJOB' => JobAdd::class,
            'DELJOB' => JobDelete::class,
            'FASTACK' => JobFastAck::class,
            'GETJOB' => JobGet::class,
            'NACK' => JobNack::class,
            'SHOW' => JobShow::class,
            'WORKING' => JobWorking::class,

            'QLEN' => QueueLength::class,
            'PAUSE' => QueuePause::class,
            'QPEEK' => QueuePeek::class,
            'QSTAT' => QueueStatistics::class,

            'JSCAN' => JobScan::class,
            'QSCAN' => QueueScan::class,

            'CONFIG' => ServerConfig::class,
            'DEBUG' => ServerDebug::class,
            'HELLO' => ServerHello::class,
            'INFO' => ServerInfo::class,
        ];
    }
}
