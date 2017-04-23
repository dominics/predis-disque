<?php

namespace Predisque\Profile;

use Predis\ClientException;

/**
 * Factory class for creating profile instances from strings.
 */
final class Factory
{
    private static $profiles = array(
        '1.0' => DisqueVersion100::class,
        'dev' => DisqueUnstable::class,
        'default' => DisqueVersion100::class,
    );

    private function __construct()
    {
        // NOOP
    }

    /**
     * Returns the default server profile.
     *
     * @return ProfileInterface
     * @throws ClientException
     */
    public static function getDefault()
    {
        return self::get('default');
    }

    /**
     * Returns the development server profile.
     *
     * @return ProfileInterface
     * @throws ClientException
     */
    public static function getDevelopment()
    {
        return self::get('dev');
    }

    /**
     * Registers a new server profile.
     *
     * @param string $alias Profile version or alias.
     * @param string $class FQN of a class implementing Predis\Profile\ProfileInterface.
     *
     * @throws \InvalidArgumentException
     */
    public static function define($alias, $class)
    {
        $reflection = new \ReflectionClass($class);

        if (!$reflection->isSubclassOf(ProfileInterface::class)) {
            throw new \InvalidArgumentException("The class '$class' is not a valid profile class.");
        }

        self::$profiles[$alias] = $class;
    }

    /**
     * Returns the specified server profile.
     *
     * @param string $version Profile version or alias.
     *
     * @throws ClientException
     *
     * @return ProfileInterface
     */
    public static function get($version)
    {
        if (!isset(self::$profiles[$version])) {
            throw new ClientException("Unknown server profile: '$version'.");
        }

        $profile = self::$profiles[$version];

        return new $profile();
    }
}
