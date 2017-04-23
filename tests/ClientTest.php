<?php

namespace Varspool\Disque;

use PHPUnit\Framework\TestCase;

class ClientTest extends TestCase
{
    public function testClient()
    {
        $client = new Client();
        $info = $client->info();

        var_dump($info);

        $this->assertTrue(true, 'woop');
    }
}
