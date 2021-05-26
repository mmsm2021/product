#!/bin/sh
set -e

if [ -d "/entrypoint.sh.d" ]; then
    for f in /entrypoint.sh.d/*.sh; do
        . "$f"
    done
fi

if [ "${1#-}" != "$1" ]; then
	set -- php-fpm "$@"
fi

exec "$@"