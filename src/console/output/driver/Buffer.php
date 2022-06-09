<?php

namespace mftd\console\output\driver;

use mftd\console\Output;
use Throwable;

class Buffer
{
    /**
     * @var string
     */
    private $buffer = '';

    public function __construct(Output $output)
    {
        // do nothing
    }

    public function fetch()
    {
        $content = $this->buffer;
        $this->buffer = '';
        return $content;
    }

    public function renderException(Throwable $e)
    {
        // do nothing
    }

    public function write($messages, bool $newline = false, int $options = 0)
    {
        $messages = (array)$messages;

        foreach ($messages as $message) {
            $this->buffer .= $message;
        }
        if ($newline) {
            $this->buffer .= "\n";
        }
    }
}
