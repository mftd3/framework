<?php


namespace mftd;

use InvalidArgumentException;
use mftd\filesystem\Driver;
use mftd\filesystem\driver\Local;
use mftd\helper\Arr;

/**
 * Class Filesystem
 * @package mftd
 * @mixin Driver
 * @mixin Local
 */
class Filesystem extends Manager
{
    protected $namespace = '\\mftd\\filesystem\\driver\\';

    /**
     * @param null|string $name
     * @return Driver
     */
    public function disk(string $name = null): Driver
    {
        return $this->driver($name);
    }

    /**
     * 获取缓存配置
     * @access public
     * @param null|string $name 名称
     * @param mixed $default 默认值
     * @return mixed
     */
    public function getConfig(string $name = null, $default = null)
    {
        if (!is_null($name)) {
            return $this->app->config->get('filesystem.' . $name, $default);
        }

        return $this->app->config->get('filesystem');
    }

    /**
     * 默认驱动
     * @return string|null
     */
    public function getDefaultDriver()
    {
        return $this->getConfig('default');
    }

    /**
     * 获取磁盘配置
     * @param string $disk
     * @param null $name
     * @param null $default
     * @return array
     */
    public function getDiskConfig($disk, $name = null, $default = null)
    {
        if ($config = $this->getConfig("disks.{$disk}")) {
            return Arr::get($config, $name, $default);
        }

        throw new InvalidArgumentException("Disk [$disk] not found.");
    }

    protected function resolveConfig(string $name)
    {
        return $this->getDiskConfig($name);
    }

    protected function resolveType(string $name)
    {
        return $this->getDiskConfig($name, 'type', 'local');
    }
}
