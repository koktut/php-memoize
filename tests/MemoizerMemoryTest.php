<?php

namespace PhpMemoizer;

/**
 * Class MemoizerTest
 * @package PhpMemoizer
 */
class MemoizerMemoryTest extends \PHPUnit_Framework_TestCase
{
    private $memoizer;

    public function setUp()
    {
        $this->memoizer = new Memoizer(new MemoryCache());
    }

    /**
     *  @covers Memoizer::memorize
     */
    public function testMemoizeMemory()
    {
        $func = $this->memoizer->memoize(
            (new \ReflectionMethod('\PhpMemoizer\MemoizerTest', 'tfunc'))->getClosure($this),
            0
        );
        echo $func(1,2);
    }

    /**
     * @param $val1
     * @param $val2
     * @return mixed
     */
    public function tfunc($val1, $val2)
    {
        return $val1 + $val2;
    }
}
