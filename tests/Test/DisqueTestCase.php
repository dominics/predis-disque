<?php

namespace Predisque\Test;

use PHPUnit\Framework\TestCase;
use Predisque\Client;
use Predisque\Profile\Factory;
use Predisque\Profile\ProfileInterface;

/**
 * @see \PredisTestCase
 */
abstract class DisqueTestCase extends TestCase
{
    /**
     * Returns a new client instance.
     *
     * @param bool $flushall
     * @param null $parameters
     * @param null $options
     * @return Client
     */
    public function getClient($flushall = true, $parameters = null, $options = null)
    {
        $profile = $this->getProfile();

        if (method_exists($this, 'getExpectedId') && !$profile->supportsCommand($id = $this->getExpectedId())) {
            $this->markTestSkipped(
                "The profile {$profile->getVersion()} does not support command {$id}"
            );
        }

        $client = $this->createClient($parameters, $options, $flushall);

        return $client;
    }

    /**
     * Returns a named array with the default connection parameters and their values.
     *
     * @return array Default connection parameters.
     */
    protected function getDefaultParametersArray()
    {
        return [
            'scheme' => 'tcp',
            'host' => DISQUE_SERVER_HOST,
            'port' => DISQUE_SERVER_PORT,
        ];
    }

    /**
     * Returns a named array with the default client options and their values.
     *
     * @return array Default connection parameters.
     */
    protected function getDefaultOptionsArray()
    {
        return [
            'profile' => DISQUE_SERVER_VERSION,
        ];
    }

    /**
     * Returns a new instance of server profile.
     *
     * @param string $version Disque profile.
     * @return ProfileInterface
     * @throws \Predis\ClientException
     */
    protected function getProfile($version = null): ProfileInterface
    {
        return Factory::get($version ?: DISQUE_SERVER_VERSION);
    }

    /**
     * Returns a new client instance.
     *
     * @param array $parameters Additional connection parameters.
     * @param array $options    Additional client options.
     * @param bool  $flushall   Flush all jobs, queues, stats before returning the client
     * @return Client
     * @throws \Predis\ClientException
     */
    protected function createClient(array $parameters = null, array $options = null, $flushall = true)
    {
        $parameters = array_merge(
            $this->getDefaultParametersArray(),
            $parameters ?: []
        );

        $options = array_merge(
            [
                'profile' => $this->getProfile(),
            ],
            $options ?: []
        );

        $client = new Client($parameters, $options);
        $client->connect();

        if ($flushall) {
            $client->debug('flushall');
        }

        return $client;
    }
}
