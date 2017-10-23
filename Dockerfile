FROM library/php:7.1.4-fpm

RUN set -e -x \
    && apt-get update \
    && apt-get install -y \
        apt-transport-https \
        nginx \
        zlib1g-dev zlib1g libmcrypt-dev libicu-dev \
        supervisor \
        libpcre3-dev \
        libc-client-dev libkrb5-dev \
    && docker-php-ext-configure imap --with-kerberos --with-imap-ssl \
    && docker-php-ext-install -j$(nproc) zip pdo_mysql mcrypt intl bcmath imap \
    && pecl install redis-3.1.2 \
    && docker-php-ext-enable redis

# Install nodejs-legacy and npm
RUN set -e -x \
	&& printf "deb https://deb.nodesource.com/node_7.x jessie main\ndeb-src https://deb.nodesource.com/node_7.x jessie main\n" > /etc/apt/sources.list.d/nodesource.list \
	&& apt-get update \
	&& apt-get install -y --allow-unauthenticated nodejs \
	&& npm update -g npm \
	&& npm install -g bower gulp jscs jshint typescript typings \
	&& npm rebuild node-sass --no-bin-links

WORKDIR /data/www

# composer
COPY composer.* /data/www/
RUN set -ex \
	&& curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer \
	&& composer install --no-progress --no-suggest --no-interaction --no-scripts --no-autoloader

# node / npm
COPY package.json /data/www/
RUN set -ex \
	&& npm install

COPY . /data/www

RUN set -e -x \
    && cp docker/nginx/nginx.conf /etc/nginx/nginx.conf \
    && cp docker/php-fpm/www.conf /usr/local/etc/php-fpm.d/www.conf \
    && cp docker/php-fpm/php.ini /usr/local/etc/php/php.ini \
    && cp docker/supervisor/supervisord.conf /etc/supervisor/supervisord.conf \
    && ./make prod

CMD ["/data/www/docker/start.sh"]
