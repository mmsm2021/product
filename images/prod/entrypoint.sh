#!/bin/sh
if [ -d "/entrypoint.sh.d" ]; then
    for f in /entrypoint.sh.d/*.sh; do
        . "$f"
    done
fi

set -e

if [ "${1#-}" != "$1" ]; then
	set -- php-fpm "$@"
fi

exec "$@"