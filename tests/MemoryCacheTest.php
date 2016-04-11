<?php

namespace PhpMemoizer;

require_once './../src/MemoryCache.php';

class MemoryCacheTest extends \PHPUnit_Framework_TestCase
{
    private $memoryCache;

    public function setUp()
    {
    }

    /**
     *
     */
    public function testMemoizeResult1()
    {
        $memoryCache = new MemoryCache();
        $func = $memoryCache->memoize((new \ReflectionMethod($this, 'funcToTest'))->getClosure($this));

        $this->assertEquals(3, $func(1, 2));
    }

    public function testMemoizeResult2()
    {
        $stub = $this->getMockBuilder('MemoryCache')->getMock();

        $stub->expects($this->once())->method('cacheResult');
        $memoryCache = new MemoryCache();
        $func = $memoryCache->memoize((new \ReflectionMethod($this, 'funcToTest'))->getClosure($this));
        $func(1, 2);
    }

    /**
     * @param $val1
     * @param $val2
     * @return mixed
     */
    public function funcToTest($val1, $val2)
    {
        return $val1 + $val2;
    }
}
