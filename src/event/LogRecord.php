<?php

namespace mftd\event;

/**
 * LogRecord事件类
 */
class LogRecord
{
    /** @var string */
    public $message;
    /** @var string */
    public $type;

    public function __construct($type, $message)
    {
        $this->type = $type;
        $this->message = $message;
    }
}
