<?php

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

    public function getDefault(OptionsInterface $options)
    {
        $profile = Factory::getDefault();
        $this->setProcessors($options, $profile);

        return $profile;
    }
}
