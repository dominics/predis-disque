<?php

use Composer\Autoload\ClassLoader;

$loader = require __DIR__ . '/../vendor/autoload.php';

/**
 * @var ClassLoader $loader
 */
$loader->addPsr4('Varspool\\Disque\\', __DIR__);
