<?php


namespace mftd\filesystem;

use Closure;
use League\Flysystem\Adapter\AbstractAdapter;
use League\Flysystem\AdapterInterface;
use League\Flysystem\Cached\CachedAdapter;
use League\Flysystem\Cached\Storage\Memory as MemoryStore;
use League\Flysystem\Filesystem;
use mftd\Cache;
use mftd\File;
use RuntimeException;

/**
 * Class Driver
 * @package mftd\filesystem
 * @mixin Filesystem
 */
abstract class Driver
{
    /** @var Cache */
    protected $cache;
    /**
     * 配置参数
     * @var array
     */
    protected $config = [];
    /** @var Filesystem */
    protected $filesystem;

    public function __construct(Cache $cache, array $config)
    {
        $this->cache = $cache;
        $this->config = array_merge($this->config, $config);

        $adapter = $this->createAdapter();
        $this->filesystem = $this->createFilesystem($adapter);
    }

    public function __call($method, $parameters)
    {
        return $this->filesystem->$method(...$parameters);
    }

    /**
     * 获取文件完整路径
     * @param string $path
     * @return string
     */
    public function path(string $path): string
    {
        $adapter = $this->filesystem->getAdapter();

        if ($adapter instanceof AbstractAdapter) {
            return $adapter->applyPathPrefix($path);
        }

        return $path;
    }

    /**
     * 保存文件
     * @param string $path 路径
     * @param File $file 文件
     * @param null|string|Closure $rule 文件名规则
     * @param array $options 参数
     * @return bool|string
     */
    public function putFile(string $path, File $file, $rule = null, array $options = [])
    {
        return $this->putFileAs($path, $file, $file->hashName($rule), $options);
    }

    /**
     * 指定文件名保存文件
     * @param string $path 路径
     * @param File $file 文件
     * @param string $name 文件名
     * @param array $options 参数
     * @return bool|string
     */
    public function putFileAs(string $path, File $file, string $name, array $options = [])
    {
        $stream = fopen($file->getRealPath(), 'r');
        $path = trim($path . '/' . $name, '/');

        $result = $this->putStream($path, $stream, $options);

        if (is_resource($stream)) {
            fclose($stream);
        }

        return $result ? $path : false;
    }

    public function url(string $path): string
    {
        throw new RuntimeException('This driver does not support retrieving URLs.');
    }

    protected function concatPathToUrl($url, $path)
    {
        return rtrim($url, '/') . '/' . ltrim($path, '/');
    }

    abstract protected function createAdapter(): AdapterInterface;

    protected function createCacheStore($config)
    {
        if (true === $config) {
            return new MemoryStore();
        }

        return new CacheStore(
            $this->cache->store($config['store']),
            $config['prefix'] ?? 'flysystem',
            $config['expire'] ?? null
        );
    }

    protected function createFilesystem(AdapterInterface $adapter): Filesystem
    {
        if (!empty($this->config['cache'])) {
            $adapter = new CachedAdapter($adapter, $this->createCacheStore($this->config['cache']));
        }

        $config = array_intersect_key($this->config, array_flip(['visibility', 'disable_asserts', 'url']));

        return new Filesystem($adapter, count($config) > 0 ? $config : null);
    }
}
