<?php

namespace Predisque\Command;

class JobAdd extends Command
{
    public function getId()
    {
        return 'ADDJOB';
    }

    /**
     * {@inheritdoc}
     */
    protected function filterArguments(array $arguments)
    {
        if (count($arguments) === 4 && is_array($arguments[3])) {
            $options = $this->prepareOptions(array_pop($arguments));
            $arguments = array_merge($arguments, $options);
        }

        return $arguments;
    }

    /**
     * Returns a list of options and modifiers compatible with Redis.
     *
     * @param array $options List of options.
     *
     * @return array
     */
    protected function prepareOptions($options)
    {
        $options = array_change_key_case($options, CASE_UPPER);
        $normalized = array();

        if (!empty($options['REPLICATE'])) {
            $normalized[] = 'REPLICATE';
            $normalized[] = $options['REPLICATE'];
        }

        if (!empty($options['DELAY'])) {
            $normalized[] = 'DELAY';
            $normalized[] = $options['DELAY'];
        }

        if (!empty($options['RETRY'])) {
            $normalized[] = 'RETRY';
            $normalized[] = $options['RETRY'];
        }

        if (!empty($options['TTL'])) {
            $normalized[] = 'TTL';
            $normalized[] = $options['TTL'];
        }

        if (!empty($options['MAXLEN'])) {
            $normalized[] = 'MAXLEN';
            $normalized[] = $options['MAXLEN'];
        }

        if (!empty($options['ASYNC'])) {
            $normalized[] = 'ASYNC';
        }

        return $normalized;
    }
}
