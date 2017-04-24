<?php

namespace Predisque;

/**
 * Simple value object to represent (part of) the response to a GETJOB command
 *
 * Like an HTTP message object, you probably don't want to extend this class, and instead
 * pass it around to your real job classes or factories.
 */
class Job
{
    /**
     * @var string
     */
    public $queue;

    /**
     * @var string|JobId
     */
    public $id;

    /**
     * @var string
     */
    public $body;

    public static function toArray(self $job): array
    {
        return [
            $job->queue,
            (string)$job->id,
            $job->body,
        ];
    }

    public static function fromArray(array $array): self
    {
        $job = new self();
        list($job->queue, $job->id, $job->body) = $array;
        return $job;
    }
}
