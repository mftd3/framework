<?php


namespace mftd\facade;

use mftd\Facade;
use mftd\filesystem\Driver;

/**
 * Class Filesystem
 * @package mftd\facade
 * @mixin \mftd\Filesystem
 * @method static Driver disk(string $name = null) , null|string
 * @method static mixed getConfig(null|string $name = null, mixed $default = null) 获取缓存配置
 * @method static array getDiskConfig(string $disk, null $name = null, null $default = null) 获取磁盘配置
 * @method static string|null getDefaultDriver() 默认驱动
 */
class Filesystem extends Facade
{
    protected static function getFacadeClass()
    {
        return 'filesystem';
    }
}
