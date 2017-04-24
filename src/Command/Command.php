<?php

namespace Predisque\Command;

use Predis\Command\Command as PredisCommand;

abstract class Command extends PredisCommand
{
    protected function parseArray($data)
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
