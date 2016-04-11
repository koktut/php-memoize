<?php

namespace PhpMemoizer;

class DiskCache extends BaseCache
{
    protected $cachDirectory;

    /**
     * @param $cacheDirectory
     */
    public function __construct($cacheDirectory)
    {
        $this->cachDirectory = $cacheDirectory;
    }

    /**
     * Clear all cached results
     */
    public function clearCache()
    {
        $files = scandir($this->cachDirectory);
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
    }

    /**
     * Clear all expired results
     */
    public function clearExpired()
    {
    }

    /**
     * @param $key
     * @return mixed
     */
    protected function isCached($key)
    {
        return file_exists($this->getCacheFileName($key));
    }

    /**
     * @param $key
     * @return bool
     */
    protected function isExpired($key)
    {
        $data = unserialize(file_get_contents($this->getCacheFileName($key)));
        if ($data['expired'] == 0) {
            return false;
        }
        return $data['expired'] < microtime(true);
    }

    /**
     * @param $key
     * @return mixed
     */
    protected function getCachedResult($key)
    {
        $data = unserialize(file_get_contents($this->getCacheFileName($key)));
        return $data['value'];
    }

    /**
     * @param $key
     * @param $expire
     * @param $value
     * @return mixed
     */
    protected function cacheResult($key, $expire, $value)
    {
        $data = [
            'expired' => $expire,
            'value' => $value
        ];
        file_put_contents($this->getCacheFileName($key), serialize($data));
    }

    /**
     * @param $key
     */
    protected function deleteResult($key)
    {
        unlink($this->getCacheFileName($key));
    }

    protected function getCacheFileName($key)
    {
        return $this->cachDirectory . DIRECTORY_SEPARATOR . $key;
    }
}
