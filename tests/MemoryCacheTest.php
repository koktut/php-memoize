<?php

use PhpMemo\MemoryCache;
use PHPUnit\Framework\TestCase;

class MemoryCacheTest extends TestCase
{
    private $testFunction;

    /**
     * @inheritDoc
     */
    public function setUp() : void
    {
        $this->testFunction = function ($val1, $val2) {
            return $val1 + $val2;
        };
    }

    /**
     * @test
     */
    public function testMemoizeResultIsCorrect()
    {
        $memoryCache = new MemoryCache();

        $func = $memoryCache->memoize($this->testFunction);

        $this->assertEquals(3, $func(1, 2));
        $this->assertEquals(3, $func(1, 2));
    }

    /**
     * @test
     */
    public function testCacheResult()
    {
        $memoryCache = $this->getMockBuilder(MemoryCache::class)
            ->onlyMethods(
                [
                    'cacheResult',
                    'getCachedResult'
                ]
            )
            ->getMock();

        $memoryCache->expects($this->once())->method('cacheResult');
        $memoryCache->expects($this->never())->method('getCachedResult');
        $func = $memoryCache->memoize($this->testFunction);
        $func(1, 2);
    }

    /**
     * @test
     */
    public function testGetCachedResult()
    {
        $memoryCache = $this->getMockBuilder(MemoryCache::class)
            ->onlyMethods(
                [
                    'getCachedResult'
                ]
            )
            ->getMock();

        $memoryCache->expects($this->once())->method('getCachedResult');
        $func = $memoryCache->memoize($this->testFunction);
        $func(1, 2);
        $func(1, 2);
    }

    /**
     * @test
     */
    public function testCacheWhenNotCached()
    {
        $memoryCache = $this->getMockBuilder(MemoryCache::class)
            ->onlyMethods(
                [
                    'isCached',
                    'cacheResult',
                    'getCachedResult'
                ]
            )
            ->getMock();

        $memoryCache->method('isCached')->will($this->returnValue(false));
        $memoryCache->expects($this->once())->method('cacheResult');
        $memoryCache->expects($this->never())->method('getCachedResult');
        $func = $memoryCache->memoize($this->testFunction);
        $func(1, 2);
    }

    /**
     * @test
     */
    public function testCacheWhenExpired()
    {
        $memoryCache = $this->getMockBuilder(MemoryCache::class)
            ->onlyMethods(
                [
                    'isCached',
                    'isExpired',
                    'cacheResult',
                    'getCachedResult'
                ]
            )
            ->getMock();

        $memoryCache->method('isCached')->will($this->returnValue(true));
        $memoryCache->method('isExpired')->will($this->returnValue(true));
        $memoryCache->expects($this->once())->method('cacheResult');
        $memoryCache->expects($this->never())->method('getCachedResult');
        $func = $memoryCache->memoize($this->testFunction);
        $func(1, 2);
    }

    /**
     * @test
     */
    public function testClearCache()
    {
        $memoryCache = $this->getMockBuilder(MemoryCache::class)
            ->onlyMethods(
                [
                    'getCachedResult'
                ]
            )
            ->getMock();

        $memoryCache->expects($this->exactly(2))->method('getCachedResult');
        $func = $memoryCache->memoize($this->testFunction);
        $func(1, 2);
        $func(1, 2);
        $memoryCache->clearCache();
        $func(1, 2);
        $func(1, 2);
    }

    /**
     * @test
     */
    public function testClearExpired()
    {
        $memoryCache = $this->getMockBuilder(MemoryCache::class)
            ->onlyMethods(
                [
                    'getCachedResult',
                    'deleteResult',
                ]
            )
            ->getMock();

        $memoryCache->expects($this->once())->method('deleteResult');
        $func = $memoryCache->memoize($this->testFunction, 1);
        $func(1, 2);
        $memoryCache->clearExpired();
        sleep(2);
        $memoryCache->clearExpired();
    }
}
