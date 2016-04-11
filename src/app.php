<?php

require_once './../vendor/autoload.php';
//namespace PhpMemoizer;

function tfunc($val1, $val2)
{
    sleep(5);
    return $val1 + $val2;
};

$memoizer = new \PhpMemoizer\DiskCache('d:/cache');
//$memoizer = new \PhpMemoizer\MemoryCache();
$func = $memoizer->memoize('tfunc', 'tfunc', 0);

echo $func(1, 2);
echo $func(1, 2);

$memoizer->clearCache();
