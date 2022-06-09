<?php

namespace mftd\session\driver;

use mftd\contract\SessionHandlerInterface;
use mftd\helper\Arr;
use Psr\SimpleCache\CacheInterface;

class Cache implements SessionHandlerInterface
{
    /** @var integer */
    protected $expire;
    /** @var CacheInterface */
    protected $handler;
    /** @var string */
    protected $prefix;

    public function __construct(\mftd\Cache $cache, array $config = [])
    {
        $this->handler = $cache->store(Arr::get($config, 'store'));
        $this->expire = Arr::get($config, 'expire', 1440);
        $this->prefix = Arr::get($config, 'prefix', '');
    }

    public function delete(string $sessionId): bool
    {
        return $this->handler->delete($this->prefix . $sessionId);
    }

    public function read(string $sessionId): string
    {
        return (string)$this->handler->get($this->prefix . $sessionId);
    }

    public function write(string $sessionId, string $data): bool
    {
        return $this->handler->set($this->prefix . $sessionId, $data, $this->expire);
    }
}
