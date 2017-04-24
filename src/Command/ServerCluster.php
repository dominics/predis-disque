<?php

namespace Predisque\Command;

/**
 * CLUSTER command
 *
 * Known subcommands:
 *   - CLUSTER MEET
 *   - CLUSTER FORGET
 *   - CLUSTER LEAVING
 *   - CLUSTER INFO
 *   - CLUSTER RESET
 */
class ServerCluster extends Command
{
    public function getId()
    {
        return 'CLUSTER';
    }

    public function parseResponse($data)
    {
        switch (strtolower($this->getArgument(0))) {
            case 'info':
                return $this->parseArray($data);
            default:
                return $data;
        }
    }
}
