<?php


namespace mftd\event;

/**
 * LogWrite事件类
 */
class LogWrite
{
    /** @var string */
    public $channel;

    /** @var array */
    public $log;

    public function __construct($channel, $log)
    {
        $this->channel = $channel;
        $this->log = $log;
    }
}
