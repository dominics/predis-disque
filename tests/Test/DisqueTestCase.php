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
     * Returns a named array with the default connection parameters and their values.
     *
     * @return array Default connection parameters.
     */
    protected function getDefaultParametersArray()
    {
        return array(
            'scheme' => 'tcp',
            'host' => DISQUE_SERVER_HOST,
            'port' => DISQUE_SERVER_PORT,
        );
    }

    /**
     * Returns a named array with the default client options and their values.
     *
     * @return array Default connection parameters.
     */
    protected function getDefaultOptionsArray()
    {
        return array(
            'profile' => DISQUE_SERVER_VERSION,
        );
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
    protected function createClient(array $parameters = null, array $options = null, $flushall = false)
    {
        $parameters = array_merge(
            $this->getDefaultParametersArray(),
            $parameters ?: array()
        );

        $options = array_merge(
            array(
                'profile' => $this->getProfile(),
            ),
            $options ?: array()
        );

        $client = new Client($parameters, $options);
        $client->connect();

        if ($flushall) {
            $client->debug('flushall');
        }

        return $client;
    }
}
