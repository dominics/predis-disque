<?php

/*
 * This file is part of the Predis package.
 *
 * (c) Daniele Alessandri <suppakilla@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Predisque\Configuration;

use Predis\Configuration\OptionsInterface;
use Predis\Configuration\ProfileOption as PredisProfileOption;
use Predisque\Profile\Factory;
use Predisque\Profile\ProfileInterface;

class ProfileOption extends PredisProfileOption
{
    public function filter(OptionsInterface $options, $value)
    {
        if (is_string($value)) {
            $value = Factory::get($value);
            $this->setProcessors($options, $value);
        } elseif (!$value instanceof ProfileInterface) {
            throw new \InvalidArgumentException('Invalid value for the profile option.');
        }

        return $value;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefault(OptionsInterface $options)
    {
        $profile = Factory::getDefault();
        $this->setProcessors($options, $profile);

        return $profile;
    }
}
