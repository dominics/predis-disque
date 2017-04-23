<?php

namespace Varspool\Disque;

use Varspool\Disque\Configuration\Options;

/**
 * @method mixed ping($message = null)
 * @method array info($section = null)
 */
class Client extends \Predis\Client
{
    protected function createOptions($options)
    {
        if (is_array($options)) {
            return new Options($options);
        }

        return parent::createOptions($options);
    }

}
