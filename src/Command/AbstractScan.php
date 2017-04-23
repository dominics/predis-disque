<?php

namespace Predisque\Command;

abstract class AbstractScan extends Command
{
    protected function filterArguments(array $arguments)
    {
        if (count($arguments) === 1 && is_array($arguments[0])
            || count($arguments) === 2 && is_array($arguments[1])
        ) {
            $options = $this->prepareOptions(array_pop($arguments));
            $arguments = array_merge($arguments, $options);
        }

        return $arguments;
    }

    protected function prepareOptions($options)
    {
        $options = array_change_key_case($options, CASE_UPPER);
        $normalized = array();

        if (!empty($options['COUNT'])) {
            $normalized[] = 'COUNT';
            $normalized[] = $options['COUNT'];
        }

        if (!empty($options['BUSYLOOP'])) {
            $normalized[] = 'BUSYLOOP';
        }

        return $normalized;
    }
}
