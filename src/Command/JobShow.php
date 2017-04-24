<?php

namespace Predisque\Command;

class JobShow extends Command
{
    public function getId()
    {
        return 'SHOW';
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
