<?php

namespace Varspool\Disque\Functional;

use Varspool\Disque\Client;
use Varspool\Disque\Test\FunctionalTestCase;

class ClientTest extends FunctionalTestCase
{
    /**
     * @group connected
     */
    public function testInfo()
    {
        $client = new Client();

        $info = $client->info();

        $this->assertArrayHasKey('Server', $info);
        $this->assertArrayHasKey('disque_version', $info['Server']);
    }
}
