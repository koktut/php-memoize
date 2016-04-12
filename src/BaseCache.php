<?php

namespace PhpMemo;

/**
 * Class BaseCache
 * @package PhpMemoizer
 */
abstract class BaseCache
{
    protected $lastOperationTime;
    
    /**
     * @param $func
     * @param int $expire - Expire time in sec. 0 - never expired
     * @param string $additionalKey - User's additional key
     * @return \Closure
     */
    public function memoize($func, $expire = 0, $additionalKey = '')
    {
        return function () use ($func, $additionalKey, $expire) {
            $args = func_get_args();
            $key = $additionalKey . '_'. md5(serialize($args));
            $starttime = microtime(true);
            if ($this->isCached($key) && !$this->isExpired($key)) {
                $result = $this->getCachedResult($key);
            } else {
                $result = $this->cacheResult($key, $expire, call_user_func_array($func, $args));
            }
            $this->lastOperationTime = microtime(true) - $starttime;
            return $result;
        };
    }

    /**
     * @return mixed
     */
    public function getLastOpTime()
    {
        return $this->lastOperationTime;
    }

    /**
     * @param $key
     * @return mixed
     */
    abstract protected function isCached($key);

    /**
     * @param $key
     * @return mixed
     */
    abstract protected function isExpired($key);

    /**
     * @param $key
     * @return mixed
     */
    abstract protected function getCachedResult($key);

    /**
     * @param $key
     * @param $expire
     * @param $value
     * @return mixed
     */
    abstract protected function cacheResult($key, $expire, $value);
}
