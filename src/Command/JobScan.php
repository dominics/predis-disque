<?php

namespace Predisque\Command;

class JobScan extends AbstractScan
{
    public function getId()
    {
        return 'JSCAN';
    }

    protected function prepareOptions($options)
    {
        $options = array_change_key_case($options, CASE_UPPER);
        $normalized = parent::prepareOptions($options);

        if (!empty($options['QUEUE'])) {
            $normalized[] = 'QUEUE';
            $normalized[] = $options['QUEUE'];
        }

        if (!empty($options['STATE'])) {
            if (!is_iterable($options['STATE'])) {
                $options['STATE'] = [$options['STATE']];
            }

            foreach ($options['STATE'] as $state) {
                $normalized[] = 'STATE';
                $normalized[] = $state;
            }
        }

        if (!empty($options['REPLY'])) {
            $normalized[] = 'REPLY';
            $normalized[] = $options['REPLY'];
        }

        return $normalized;
    }
}
