<?php

declare(strict_types=1);

namespace think\db\exception;

use Psr\SimpleCache\InvalidArgumentException as SimpleCacheInvalidArgumentInterface;

/**
 * 非法数据异常
 */
class InvalidArgumentException extends \InvalidArgumentException implements SimpleCacheInvalidArgumentInterface
{
}