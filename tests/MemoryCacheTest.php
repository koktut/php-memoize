<?php

namespace PhpMemoizer;

class MemoryCacheTest extends \PHPUnit_Framework_TestCase
{
    private $testFunction;

    public function setUp()
    {
        $this->testFunction = function ($val1, $val2) {
            return $val1 + $val2;
        };
    }

    public function testMemoizeResultIsCorrect()
    {
        $memoryCache = new MemoryCache();
        $func = $memoryCache->memoize($this->testFunction);

        $this->assertEquals(3, $func(1, 2));
        $this->assertEquals(3, $func(1, 2));
    }

    public function testCacheResult()
    {
        $memoryCache = $this->getMock(
            '\PhpMemoizer\MemoryCache',
            [
                'cacheResult',
                'getCachedResult'
            ]
        );

        $memoryCache->expects($this->once())->method('cacheResult');
        $memoryCache->expects($this->never())->method('getCachedResult');
        $func = $memoryCache->memoize($this->testFunction);
        $func(1, 2);
    }

    public function testGetCachedResult()
    {
        $memoryCache = $this->getMock(
            '\PhpMemoizer\MemoryCache',
            [
                'getCachedResult'
            ]
        );
        $memoryCache->expects($this->once())->method('getCachedResult');
        $func = $memoryCache->memoize($this->testFunction);
        $func(1, 2);
        $func(1, 2);
    }

    public function testCacheWhenNotCached()
    {
        $memoryCache = $this->getMock(
            '\PhpMemoizer\MemoryCache',
            [
                'isCached',
                'cacheResult',
                'getCachedResult'
            ]
        );
        $memoryCache->method('isCached')->will($this->returnValue(false));
        $memoryCache->expects($this->once())->method('cacheResult');
        $memoryCache->expects($this->never())->method('getCachedResult');
        $func = $memoryCache->memoize($this->testFunction);
        $func(1, 2);
    }

    public function testCacheWhenExpired()
    {
        $memoryCache = $this->getMock(
            '\PhpMemoizer\MemoryCache',
            [
                'isCached',
                'isExpired',
                'cacheResult',
                'getCachedResult'
            ]
        );
        $memoryCache->method('isCached')->will($this->returnValue(true));
        $memoryCache->method('isExpired')->will($this->returnValue(true));
        $memoryCache->expects($this->once())->method('cacheResult');
        $memoryCache->expects($this->never())->method('getCachedResult');
        $func = $memoryCache->memoize($this->testFunction);
        $func(1, 2);
    }

    public function testClearCache()
    {
        $memoryCache = $this->getMock(
            '\PhpMemoizer\MemoryCache',
            [
                'getCachedResult'
            ]
        );
        $memoryCache->expects($this->exactly(2))->method('getCachedResult');
        $func = $memoryCache->memoize($this->testFunction);
        $func(1, 2);
        $func(1, 2);
        $memoryCache->clearCache();
        $func(1, 2);
        $func(1, 2);
    }

    public function testClearExpired()
    {
        $memoryCache = $this->getMock(
            '\PhpMemoizer\MemoryCache',
            [
                'deleteResult'
            ]
        );
        $memoryCache->expects($this->once(1))->method('deleteResult');
        $func = $memoryCache->memoize($this->testFunction, '', 1);
        $func(1, 2);
        $memoryCache->clearExpired();
        sleep(2);
        $memoryCache->clearExpired();
    }

}
