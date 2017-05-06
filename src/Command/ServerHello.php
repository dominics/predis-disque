<?php

namespace Predisque\Command;

class ServerHello extends Command
{
    public const VERSION_1 = 1;

    public function getId()
    {
        return 'HELLO';
    }

    /**
     * @param string $data
     * @return array|string
     */
    public function parseResponse($data)
    {
        if (is_array($data) && count($data) > 2 && $data[0] === self::VERSION_1) {
            return [
                $data[0],
                $data[1],
                array_slice($data, 2)
            ];
        }

        return $data;
    }
}
