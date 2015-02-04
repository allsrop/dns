#!/usr/bin/make -f

.PHONY: docs check update update-dep test phar pux db

check:
	find src -name '*.php' -exec php -l {} \;
	find test -name '*.php' -exec php -l {} \;
	find src -name '*.php' -exec vendor/bin/phpcs --standard=PSR2 {} \;

docs:
	doxygen doxygen.conf

composer.phar:
	curl -sS https://getcomposer.org/installer | php

update: composer.phar
	./composer.phar install
	vendor/bin/fruit lazy -c config.ini schema

db:
	vendor/bin/fruit lazy -c config.ini create-db
	vendor/bin/fruit lazy -c config.ini sql -r

force-update: composer.phar 
	./composer.phar selfupdate
	./composer.phar update

pux:
	vendor/bin/fruit pux -o pux/route.php pux/mux.php

test: update
	vendor/bin/phpunit -c phpunit.xml
