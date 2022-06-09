<?php


namespace mftd;

use DateInterval;
use DateTime;
use mftd\cache\Driver;
use mftd\cache\TagSet;
use mftd\exception\InvalidArgumentException;
use mftd\helper\Arr;
use Psr\SimpleCache\CacheInterface;

/**
 * 缓存管理类
 * @mixin Driver
 * @mixin \mftd\cache\driver\File
 */
class Cache extends Manager implements CacheInterface
{
    protected $namespace = '\\mftd\\cache\\driver\\';

    /**
     * 清空缓冲池
     * @access public
     * @return bool
     */
    public function clear(): bool
    {
        return $this->store()->clear();
    }

    /**
     * 删除缓存
     * @access public
     * @param string $key 缓存变量名
     * @return bool
     */
    public function delete($key): bool
    {
        return $this->store()->delete($key);
    }

    /**
     * 删除缓存
     * @access public
     * @param iterable $keys 缓存变量名
     * @return bool
     * @throws InvalidArgumentException
     */
    public function deleteMultiple($keys): bool
    {
        return $this->store()->deleteMultiple($keys);
    }

    /**
     * 读取缓存
     * @access public
     * @param string $key 缓存变量名
     * @param mixed $default 默认值
     * @return mixed
     */
    public function get($key, $default = null)
    {
        return $this->store()->get($key, $default);
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
            return $this->app->config->get('cache.' . $name, $default);
        }

        return $this->app->config->get('cache');
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
     * 读取缓存
     * @access public
     * @param iterable $keys 缓存变量名
     * @param mixed $default 默认值
     * @return iterable
     * @throws InvalidArgumentException
     */
    public function getMultiple($keys, $default = null): iterable
    {
        return $this->store()->getMultiple($keys, $default);
    }

    /**
     * 获取驱动配置
     * @param string $store
     * @param string $name
     * @param null $default
     * @return array
     */
    public function getStoreConfig(string $store, string $name = null, $default = null)
    {
        if ($config = $this->getConfig("stores.{$store}")) {
            return Arr::get($config, $name, $default);
        }

        throw new \InvalidArgumentException("Store [$store] not found.");
    }

    /**
     * 判断缓存是否存在
     * @access public
     * @param string $key 缓存变量名
     * @return bool
     */
    public function has($key): bool
    {
        return $this->store()->has($key);
    }

    /**
     * 写入缓存
     * @access public
     * @param string $key 缓存变量名
     * @param mixed $value 存储数据
     * @param int|DateTime $ttl 有效时间 0为永久
     * @return bool
     */
    public function set($key, $value, $ttl = null): bool
    {
        return $this->store()->set($key, $value, $ttl);
    }

    /**
     * 写入缓存
     * @access public
     * @param iterable $values 缓存数据
     * @param null|int|DateInterval $ttl 有效时间 0为永久
     * @return bool
     */
    public function setMultiple($values, $ttl = null): bool
    {
        return $this->store()->setMultiple($values, $ttl);
    }

    /**
     * 连接或者切换缓存
     * @access public
     * @param string $name 连接配置名
     * @return Driver
     */
    public function store(string $name = null)
    {
        return $this->driver($name);
    }

    /**
     * 缓存标签
     * @access public
     * @param string|array $name 标签名
     * @return TagSet
     */
    public function tag($name): TagSet
    {
        return $this->store()->tag($name);
    }

    protected function resolveConfig(string $name)
    {
        return $this->getStoreConfig($name);
    }

    protected function resolveType(string $name)
    {
        return $this->getStoreConfig($name, 'type', 'file');
    }
}
