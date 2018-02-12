#!/bin/bash

set -e

function clean {
   composer dump-autoload
}

function prod {
	set -x
	composer install -o --classmap-authoritative
	clear
    ./artisan config:cache
    # ./artisan route:cache
    # npm run prod
}

function dev {
	set -x
	composer install
	clear
	npm install
    npm run dev
    ./artisan migrate
}

function clear {
	./artisan cache:clear
	./artisan route:clear
	./artisan config:clear
}

function test {
	vendor/bin/phpunit
}

function update {
	npm install
	dev
	./artisan migrate
}

command="$1"

case $command in
    clean)
    clean
    ;;
    prod)
    prod
    ;;
    dev)
    dev
    ;;
    clear)
    clear
    ;;
    update)
    update
    ;;
    test)
    test
    ;;
    *)
    echo "Unknown command $1"
    exit 1
    ;;
esac
