<?php

namespace mftd\console\output\driver;

use mftd\console\Output;
use Throwable;

class Nothing
{
    public function __construct(Output $output)
    {
        // do nothing
    }

    public function renderException(Throwable $e)
    {
        // do nothing
    }

    public function write($messages, bool $newline = false, int $options = 0)
    {
        // do nothing
    }
}
