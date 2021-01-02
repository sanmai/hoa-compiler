.PHONY: all
all: test

.PHONY: test
test: cs
	if [ -f vendor/bin/atoum ]; then php vendor/bin/atoum -ncc -d atoum/Unit/; fi
	php vendor/bin/phpunit --coverage-text

.PHONY: cs
cs: vendor/autoload.php
	php vendor/bin/php-cs-fixer fix -v

vendor/autoload.php: composer.json
	composer update && touch vendor/autoload.php
