<?php

namespace Predisque\Command;

use Predisque\Job;

class JobGet extends Command
{
    public function getId()
    {
        return 'GETJOB';
    }

    /**
     * Expects any of:
     *  array $queues, array $options
     *  string $queue, string $queue2, ..., array $options
     *  literal string format: ('NOHANG', 'FROM', string $queue1, string $queue2)
     *
     * @param array $arguments
     * @return array
     */
    protected function filterArguments(array $arguments)
    {
        if (count($arguments) === 2 && is_array($arguments[0]) && is_array($arguments[1])) {
            $queues = $arguments[0];
            $options = $this->prepareOptions($arguments[1]);

            $arguments = array_merge($options, ['FROM'], $queues);
        } elseif (count($arguments) >= 2 && is_array($arguments[count($arguments) - 1])) {
            $queues = array_slice($arguments, 0, -1);
            $options = $this->prepareOptions($arguments[count($arguments) - 1]);

            $arguments = array_merge($options, ['FROM'], $queues);
        }

        return $arguments;
    }

    /**
     * Returns a list of options and modifiers compatible with Disque
     *
     * @param array $options List of options
     *
     * @return array
     */
    protected function prepareOptions($options)
    {
        $options = array_change_key_case($options, CASE_UPPER);
        $normalized = [];

        if (!empty($options['NOHANG'])) {
            $normalized[] = 'NOHANG';
        }

        if (!empty($options['TIMEOUT'])) {
            $normalized[] = 'TIMEOUT';
            $normalized[] = $options['TIMEOUT'];
        }

        if (!empty($options['COUNT'])) {
            $normalized[] = 'COUNT';
            $normalized[] = $options['COUNT'];
        }

        if (!empty($options['WITHCOUNTERS'])) {
            $normalized[] = 'WITHCOUNTERS';
        }

        return $normalized;
    }

    public function parseResponse($data)
    {
        if (is_array($data)) {
            $response = [];

            foreach ($data as $item) {
                if (is_array($item)) {
                    $response[] = Job::fromArray($item);
                } else {
                    return $data;
                }
            }

            return $response;
        }

        return $data;
    }
}
