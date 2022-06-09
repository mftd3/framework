<?php


namespace mftd;

use mftd\helper\Arr;
use mftd\session\Store;

/**
 * Session管理类
 * @package mftd
 * @mixin Store
 */
class Session extends Manager
{
    protected $namespace = '\\mftd\\session\\driver\\';

    /**
     * 获取Session配置
     * @access public
     * @param null|string $name 名称
     * @param mixed $default 默认值
     * @return mixed
     */
    public function getConfig(string $name = null, $default = null)
    {
        if (!is_null($name)) {
            return $this->app->config->get('session.' . $name, $default);
        }

        return $this->app->config->get('session');
    }

    /**
     * 默认驱动
     * @return string|null
     */
    public function getDefaultDriver()
    {
        return $this->app->config->get('session.type', 'file');
    }

    protected function createDriver(string $name)
    {
        $handler = parent::createDriver($name);

        return new Store($this->getConfig('name') ?: 'PHPSESSID', $handler, $this->getConfig('serialize'));
    }

    protected function resolveConfig(string $name)
    {
        $config = $this->app->config->get('session', []);
        Arr::forget($config, 'type');
        return $config;
    }
}
