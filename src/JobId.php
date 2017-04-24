<?php

namespace Predisque;

class JobId
{
    const PREFIX = 'D';

    protected $nodePrefix;
    protected $random;
    protected $ttl;

    public function __construct(string $id)
    {
        $parts = explode('-', $id);

        if (count($parts) != 4 || $parts[0] !== self::PREFIX) {
            throw new PredisqueException('Invalid job ID format: ' . $id);
        }

        $this->nodePrefix = $parts[1];
        $this->random = $parts[2];
        $this->ttl = $parts[3];

        if (strlen($this->nodePrefix) !== 8) {
            throw new PredisqueException('Invalid job ID format: node prefix invalid size');
        }
    }

    public function __toString()
    {
        return implode('-', [
            self::PREFIX,
            $this->nodePrefix,
            $this->random,
            $this->ttl,
        ]);
    }
}
