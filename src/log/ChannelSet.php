<?php


namespace mftd\log;

use mftd\Log;

/**
 * Class ChannelSet
 * @package mftd\log
 * @mixin Channel
 */
class ChannelSet
{
    protected $channels;
    protected $log;

    public function __construct(Log $log, array $channels)
    {
        $this->log = $log;
        $this->channels = $channels;
    }

    public function __call($method, $arguments)
    {
        foreach ($this->channels as $channel) {
            $this->log->channel($channel)->{$method}(...$arguments);
        }
    }
}
