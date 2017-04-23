<?php

namespace Predisque\Command;

use Predis\Command\Command;

/**
 * @see \Predis\Command\KeyScan
 */
class QueueScan extends AbstractScan
{
    public function getId()
    {
        return 'QSCAN';
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
        $normalized = parent::prepareOptions($options);

        if (!empty($options['MINLEN'])) {
            $normalized[] = 'MINLEN';
            $normalized[] = $options['MINLEN'];
        }

        if (!empty($options['MAXLEN'])) {
            $normalized[] = 'MAXLEN';
            $normalized[] = $options['MAXLEN'];
        }

        if (!empty($options['IMPORTRATE'])) {
            $normalized[] = 'IMPORTRATE';
            $normalized[] = $options['IMPORTRATE'];
        }

        return $normalized;
    }
}
