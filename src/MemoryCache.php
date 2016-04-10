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
     *
     */
    public function memoize($func, $key)
    {
        return function () use ($func, $key) {
            $args = func_get_args();
            if (!$this->isCached($key)) {

            }
        };
    }

    /**
     * @param $key
     * @return mixed
     */
    protected function isCached($key)
    {
        return isset($cache[$key]);
    }
}
