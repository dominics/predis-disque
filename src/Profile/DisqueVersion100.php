<?php

namespace Predisque\Profile;

use Predisque\Command\JobAck;
use Predisque\Command\JobAdd;
use Predisque\Command\JobDelete;
use Predisque\Command\JobFastAck;
use Predisque\Command\JobGet;
use Predisque\Command\JobNack;
use Predisque\Command\JobScan;
use Predisque\Command\JobShow;
use Predisque\Command\JobWorking;
use Predisque\Command\QueueLength;
use Predisque\Command\QueuePause;
use Predisque\Command\QueuePeek;
use Predisque\Command\QueueScan;
use Predisque\Command\QueueStatistics;
use Predisque\Command\ServerConfig;
use Predisque\Command\ServerDebug;
use Predisque\Command\ServerHello;
use Predisque\Command\ServerInfo;

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
