<?php

namespace PhpMemoizer;

/**
 * Class BaseCache
 * @package PhpMemoizer
 */
abstract class BaseCache
{
    /**
     * @param $func
     * @param string $additionalKey
     * @param int $expire
     * @return \Closure
     */
    public function memoize($func, $additionalKey = '', $expire = 0)
    {
        return function () use ($func, $additionalKey, $expire) {
            $args = func_get_args();
            $key = $additionalKey . '_'. md5(serialize($args));
            if ($this->isCached($key) && !$this->isExpired($key)) {
                return $this->getCachedResult($key);
            }
            return $this->cacheResult($key, $expire, call_user_func_array($func, $args));
        };
    }

    abstract protected function isCached($key);
    abstract protected function isExpired($key);
    abstract protected function getCachedResult($key);
    abstract protected function cacheResult($key, $expire, $value);
}
