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

    protected function filterArguments(array $arguments)
    {
        $subcommand = $arguments[0];

        return parent::filterArguments($arguments);
    }

    public function parseResponse($data)
    {
        if (is_array($data)) {
            $result = [];

            for ($i = 0; $i < count($data); ++$i) {
                $result[$data[$i]] = $data[++$i];
            }

            return $result;
        }

        return $data;
    }
}
