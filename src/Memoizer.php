<?php

namespace PhpMemoizer;

/**
 * Class Memoizer
 * @package PhpMemoizer
 */
class Memoizer
{
    private $cache;

    /**
     * Memoizer constructor.
     * @param $cache
     */
    public function __construct($cache)
    {
        $this->cache = $cache;
    }

    /**
     * @param $func
     * @param $expire
     * @return mixed
     */
    public function memoize($func, $key, $expire = 0)
    {
        return $this->memo->memoize($func, $key, $expire);
    }
}
