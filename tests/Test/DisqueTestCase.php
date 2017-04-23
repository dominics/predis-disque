<?php

namespace Varspool\Disque\Test;

use PHPUnit\Framework\TestCase;
use Varspool\Disque\Profile\Factory;
use Varspool\Disque\Profile\ProfileInterface;

abstract class DisqueTestCase extends TestCase
{
    /**
     * Returns a new instance of server profile.
     *
     * @param string $version Disque profile.
     *
     * @return ProfileInterface
     */
    protected function getProfile($version = null): ProfileInterface
    {
        return Factory::get($version ?: DISQUE_SERVER_VERSION);
    }
}
