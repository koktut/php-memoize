<?php

namespace PhpMemoizer;

/**
 * Class MemoryCache
 * @package PhpMemoizer
 */
class MemoryCache extends BaseCache
{
    private $cache;

    /**
     * @param $key
     * @return mixed
     */
    protected function isCached($key)
    {
        return isset($this->cache[$key]);
    }

    /**
     * Clear all cached results
     */
    public function clearCache()
    {
        $this->cache = [];
    }

    /**
     * Clear expired results
     */
    public function clearExpired()
    {
        foreach (array_keys($this->cache) as $key) {
            if ($this->isExpired($key)) {
                $this->deleteResult($key);
            }
        }
    }

    /**
     * @param $key
     * @return bool
     */
    protected function isExpired($key)
    {
        if ($this->cache[$key]['expired'] == 0) {
            return false;
        }
        return $this->cache[$key]['expired'] < microtime(true);
    }

    /**
     * @param $key
     * @return mixed
     */
    protected function getCachedResult($key)
    {
        return $this->cache[$key]['value'];
    }

    /**
     * @param $key
     * @param $expire
     * @param $value
     * @return mixed
     */
    protected function cacheResult($key, $expire, $value)
    {
        $this->cache[$key] = [
            'expired' => $expire != 0 ? microtime(true) + $expire : 0,
            'value' => $value
        ];
        return $value;
    }

    /**
     * @param $key
     */
    protected function deleteResult($key)
    {
        unset($this->cache[$key]);
    }
}
