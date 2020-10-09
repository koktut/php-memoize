<?php

use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;
use PhpMemo\DiskCache;

class DiskCacheTest extends TestCase
{
    private $testFunction;
    private $cacheDirectory;

    /**
     * @inheritDoc
     */
    public function setUp(): void
    {
        $this->testFunction = function ($val1, $val2) {
            return $val1 + $val2;
        };

        $this->cacheDirectory = vfsStream::setup('cachdir');
    }

    /**
     * @test
     */
    public function testMemoizeResultIsCorrect()
    {
        $memoryCache = new DiskCache($this->cacheDirectory->url());

        $func = $memoryCache->memoize($this->testFunction);

        $this->assertEquals(3, $func(1, 2));
        $this->assertEquals(3, $func(1, 2));
    }

    /**
     * @covers ::cacheResult
     */
    public function testCacheResult()
    {
        $memoryCache = $this->getMockBuilder(DiskCache::class)
            ->setConstructorArgs([$this->cacheDirectory->url()])
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
     * @covers ::getCachedResult
     */
    public function testGetCachedResult()
    {
        $memoryCache = $this->getMockBuilder(DiskCache::class)
            ->setConstructorArgs([$this->cacheDirectory->url()])
            ->onlyMethods(['getCachedResult'])
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
        $memoryCache = $this->getMockBuilder(DiskCache::class)
            ->setConstructorArgs([$this->cacheDirectory->url()])
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
        $memoryCache = $this->getMockBuilder(DiskCache::class)
            ->setConstructorArgs([$this->cacheDirectory->url()])
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
     * @covers ::clearCache
     */
    public function testClearCache()
    {
        $memoryCache = $this->getMockBuilder(DiskCache::class)
            ->setConstructorArgs([$this->cacheDirectory->url()])
            ->onlyMethods(
                [
                    'getCachedResult',
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
     * @covers ::clearExpire
     */
    public function testClearExpired()
    {
        $memoryCache = $this->getMockBuilder(DiskCache::class)
            ->setConstructorArgs([$this->cacheDirectory->url()])
            ->onlyMethods(
                [
                    'deleteResult'
                ]
            )
            ->getMock();

        $memoryCache->expects($this->once(1))->method('deleteResult');
        $func = $memoryCache->memoize($this->testFunction, 1);
        $func(1, 2);
        $memoryCache->clearExpired();
        sleep(2);
        $memoryCache->clearExpired();
    }
}
