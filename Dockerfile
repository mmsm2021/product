FROM php:7.4-fpm-alpine

WORKDIR /var/www/html

RUN apk add --update --no-cache \
    libressl-dev \
    zlib-dev \
    postgresql-dev \
    nginx && \
    apk add --update --no-cache --virtual buildDeps \
    autoconf \
    gcc \
    make \
    libxml2-dev \
    curl \
    tzdata \
    curl-dev \
    oniguruma-dev \
    g++ && \
    pecl install mongodb && \
    docker-php-ext-install \
    pgsql \
    xml \
    simplexml \
    curl \
    pdo \
    pdo_pgsql \
    mysqli \
    mbstring \
    json \
    sockets \
    posix \
    bcmath && \
    apk del buildDeps && \
    docker-php-ext-enable mongodb

COPY --chown=www-data:www-data src /var/www/html
COPY images/prod/config/default.conf /etc/nginx/conf.d/default.conf
COPY images/prod/config/nginx.conf /etc/nginx/nginx.conf
COPY images/prod/config/php-fpm.conf /usr/local/etc/php-fpm.d/zz-docker.conf
COPY images/prod/entrypoint.sh /entrypoint.sh
COPY images/prod/scripts /entrypoint.sh.d

RUN chmod -R a+x /entrypoint.sh.d && \
    chmod a+x /entrypoint.sh && \
    rm -rf src/vendor; wget https://getcomposer.org/download/latest-2.x/composer.phar --output-document=/usr/local/bin/composer && \
    chmod +x /usr/local/bin/composer && \
    composer install && \
    php ./openapi.php > ./static/swagger.json && \
    chown www-data:www-data ./static/swagger.json && \
    rm -f ./static/.gitkeep && \
    rm -rf ./vendor && \
    composer install --no-dev && \
    rm -f /usr/local/bin/composer

EXPOSE 80
CMD php-fpm && sh /entrypoint.sh && nginx -g "daemon off;"
