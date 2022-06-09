<?php


namespace mftd\exception;

use Exception;
use RuntimeException;

/**
 * HTTP异常
 */
class HttpException extends RuntimeException
{
    private $headers;
    private $statusCode;

    public function __construct(int $statusCode, string $message = '', Exception $previous = null, array $headers = [], $code = 0)
    {
        $this->statusCode = $statusCode;
        $this->headers = $headers;

        parent::__construct($message, $code, $previous);
    }

    public function getHeaders()
    {
        return $this->headers;
    }

    public function getStatusCode()
    {
        return $this->statusCode;
    }
}
