install:
	composer install

autoload:
	composer dump-autoload

test:
	./vendor/bin/phpunit --color tests --coverage-clover build/logs/clover.xml
	./vendor/bin/test-reporter
