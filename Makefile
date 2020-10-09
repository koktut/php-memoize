install:
	composer install

autoload:
	composer dump-autoload

test:
	composer exec 'phpunit --color tests --coverage-clover build/logs/clover.xml'
	composer exec 'test-reporter'
