<?php

namespace Predisque;

use Predis\Response\ResponseInterface;

class JobId implements ResponseInterface
{
    const PREFIX = 'D';

    public $id;
    public $nodePrefix;
    public $random;
    public $ttl;

    public function __construct(string $id)
    {
        $this->id = $id;
    }

    public function parse(): void
    {
        $parts = explode('-', $this->id);

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

    public function fromParts(): string
    {
        return implode('-', $this->getParts());
    }

    public function getParts(): array
    {
        return [
            self::PREFIX,
            $this->nodePrefix,
            $this->random,
            $this->ttl,
        ];
    }

    public function __toString()
    {
        return $this->id;
    }
}
