<?php

namespace PhpMemo;

use org\bovigo\vfs\vfsStream;

class DiskCacheTest extends \PHPUnit_Framework_TestCase
{
    private $testFunction;
    private $cacheDirectory;

    public function setUp()
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
        $memoryCache = $this->getMock(
            '\PhpMemo\DiskCache',
            [
                'cacheResult',
                'getCachedResult'
            ],
            array($this->cacheDirectory->url())
        );

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
        $memoryCache = $this->getMock(
            '\PhpMemo\DiskCache',
            [
                'getCachedResult'
            ],
            array($this->cacheDirectory->url())
        );
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
        $memoryCache = $this->getMock(
            '\PhpMemo\DiskCache',
            [
                'isCached',
                'cacheResult',
                'getCachedResult'
            ],
            array($this->cacheDirectory->url())
        );
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
        $memoryCache = $this->getMock(
            '\PhpMemo\DiskCache',
            [
                'isCached',
                'isExpired',
                'cacheResult',
                'getCachedResult'
            ],
            array($this->cacheDirectory->url())
        );
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
        $memoryCache = $this->getMock(
            '\PhpMemo\DiskCache',
            [
                'getCachedResult'
            ],
            array($this->cacheDirectory->url())
        );
        $memoryCache->expects($this->exactly(2))->method('getCachedResult');
        $func = $memoryCache->memoize($this->testFunction);
        $func(1, 2);
        $func(1, 2);
        $memoryCache->clearCache();
        $func(1, 2);
        $func(1, 2);
    }

    /**
     * @covers clearExpired
     */
    public function testClearExpired()
    {
        $memoryCache = $this->getMock(
            '\PhpMemo\DiskCache',
            [
                'deleteResult'
            ],
            array($this->cacheDirectory->url())
        );
        $memoryCache->expects($this->once(1))->method('deleteResult');
        $func = $memoryCache->memoize($this->testFunction, 1);
        $func(1, 2);
        $memoryCache->clearExpired();
        sleep(2);
        $memoryCache->clearExpired();
    }

    /**
    * clear up test environment
    */
    public function tearDown()
    {
    }
}
