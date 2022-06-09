<?php


namespace mftd\filesystem;

use League\Flysystem\Cached\Storage\AbstractCache;
use Psr\SimpleCache\CacheInterface;

class CacheStore extends AbstractCache
{
    protected $expire;
    protected $key;
    protected $store;

    public function __construct(CacheInterface $store, $key = 'flysystem', $expire = null)
    {
        $this->key = $key;
        $this->store = $store;
        $this->expire = $expire;
    }

    /**
     * Load the cache.
     */
    public function load()
    {
        $contents = $this->store->get($this->key);

        if (!is_null($contents)) {
            $this->setFromStorage($contents);
        }
    }

    /**
     * Store the cache.
     */
    public function save()
    {
        $contents = $this->getForStorage();

        $this->store->set($this->key, $contents, $this->expire);
    }
}
