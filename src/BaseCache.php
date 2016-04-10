<?php

namespace PhpMemoizer;

/**
 * Class BaseCache
 * @package PhpMemoizer
 */
abstract class BaseCache
{
    abstract protected function isCached($key);
}
