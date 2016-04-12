<?php

namespace PhpMemo;

class DiskCache extends BaseCache
{
    protected $cacheDirectory;

    /**
     * @param $cacheDirectory
     */
    public function __construct($cacheDirectory)
    {
        $this->cacheDirectory = $cacheDirectory;
    }

    /**
     * Clear all cached results
     */
    public function clearCache()
    {
        $files = scandir($this->cacheDirectory);
        foreach ($files as $file) {
            if (is_file($this->cacheDirectory . DIRECTORY_SEPARATOR . $file)) {
                $this->deleteResult($file);
            }
        }
    }

    /**
     * Clear expired results
     */
    public function clearExpired()
    {
        $files = scandir($this->cacheDirectory);
        foreach ($files as $file) {
            if (is_file($this->cacheDirectory . DIRECTORY_SEPARATOR . $file)) {
                if ($this->isExpired($file)) {
                    $this->deleteResult($file);
                }
            }
        }
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
            'expired' => $expire != 0 ? microtime(true) + $expire : 0,
            'value' => $value
        ];
        file_put_contents($this->getCacheFileName($key), serialize($data));
        return $value;
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
        return $this->cacheDirectory . DIRECTORY_SEPARATOR . $key;
    }
}
