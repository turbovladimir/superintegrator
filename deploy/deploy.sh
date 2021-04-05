#!/usr/bin/env bash

git reset --hard
git pull
/usr/local/bin/composer-php7.2 install
php7.2 bin/console doctrine:migrations:migrate
php7.2 ./bin/phpunit