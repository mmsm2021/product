#!/bin/sh

if [ ! "$(php ./vendor/bin/doctrine-migrations up-to-date)" ]; then
    php ./vendor/bin/doctrine-migrations migrate --no-interaction || {echo "Failed to run migrations" && exit 1};
fi
echo "Migrations.sh was successful";