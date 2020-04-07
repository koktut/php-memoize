### Implementation of memoization on PHP

[![Code Climate](https://codeclimate.com/github/koktut/php-memoize/badges/gpa.svg)](https://codeclimate.com/github/koktut/php-memoize)
[![Issue Count](https://codeclimate.com/github/koktut/php-memoize/badges/issue_count.svg)](https://codeclimate.com/github/koktut/php-memoize)
[![Test Coverage](https://codeclimate.com/github/koktut/php-memoize/badges/coverage.svg)](https://codeclimate.com/github/koktut/php-memoize/coverage)

## Install
```
composer require koktut/php-memoize
```

## Usage

```
use \PhpMemo\MemoryCache;
use \PhpMemo\DiskCache;

$slowFunction = function ($val1, $val2) {
    // do something slow
    sleep(1);

    return $val1 + $val2;
};

$memo = new MemoryCache();              // or $memo = new DiskCache(sys_get_temp_dir());
$func= $memo->memoize($slowFunction);

// first call
echo $func(1, 2) . ' ' . sprintf("%f\n", $memo->getLastOpTime());   // 3 1.000000

// second call
echo $func(1, 2) . ' ' . sprintf("%f\n", $memo->getLastOpTime());   // 3 0.000000
```
