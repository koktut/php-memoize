<?php

require_once './../vendor/autoload.php';
//namespace PhpMemoizer;

function tfunc($val1, $val2)
{
    return $val1 + $val2;
};

$memoizer = new \PhpMemoizer\DiskCache('d:/cache');
//$memoizer = new \PhpMemoizer\MemoryCache();
$func = $memoizer->memoize('tfunc', 'tfunc', 10);

$memoizer->clearCache();

echo $func(1, 2);
echo $func(1, 2);

$memoizer->clearExpired();
sleep(11);
$memoizer->clearExpired();
