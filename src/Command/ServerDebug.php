<?php

namespace Predisque\Command;

use Predis\Command\Command;

class ServerDebug extends Command
{
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return 'DEBUG';
    }

    /**
     * {@inheritdoc}
     */
    public function parseResponse($data)
    {
        if (is_array($data)) {
            $result = array();

            for ($i = 0; $i < count($data); ++$i) {
                $result[$data[$i]] = $data[++$i];
            }

            return $result;
        }

        return $data;
    }
}
