install:
	composer install

autoload:
	composer dump-autoload

lint:
	composer exec 'phpcs --standard=PSR2 src tests'

test:
	composer exec 'phpunit --color tests --coverage-clover build/logs/clover.xml'
	composer exec 'test-reporter'
