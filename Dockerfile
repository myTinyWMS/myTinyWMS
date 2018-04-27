FROM library/php:7.1.4-fpm

RUN set -e -x \
    && apt-get update \
    && apt-get install -y \
        apt-transport-https \
        nginx \
        wget nano \
        zlib1g-dev zlib1g libmcrypt-dev libicu-dev \
        supervisor \
        libpcre3-dev \
        libc-client-dev libkrb5-dev \
        libpq-dev \
        libldap2-dev libxrender1 libxext6 \
        locales git \
        libfreetype6-dev libmcrypt-dev libpng12-dev libjpeg-dev libpng-dev \
    && docker-php-ext-configure imap --with-kerberos --with-imap-ssl \
    && docker-php-ext-install -j$(nproc) zip pdo_mysql mcrypt intl bcmath imap pgsql iconv  \
    && docker-php-ext-configure pgsql \
    && docker-php-ext-install pdo pdo_pgsql pgsql \
    && docker-php-ext-configure ldap --with-libdir=lib/x86_64-linux-gnu/ \
    && docker-php-ext-install ldap \
    && docker-php-ext-configure gd \
            --enable-gd-native-ttf \
            --with-freetype-dir=/usr/include/freetype2 \
            --with-png-dir=/usr/include \
            --with-jpeg-dir=/usr/include \
    && docker-php-ext-install gd \
    && docker-php-ext-install mbstring \
    && docker-php-ext-enable opcache gd \
    && pecl install redis-3.1.2 xdebug \
    && docker-php-ext-enable redis \
    && sed -i '/^#.* de_DE.* /s/^#//' /etc/locale.gen \
    && locale-gen

# Install nodejs-legacy and npm
RUN set -e -x \
    && wget --quiet -O - https://deb.nodesource.com/gpgkey/nodesource.gpg.key | apt-key add - \
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
    && mkdir -p storage/framework/cache

CMD ["/data/www/docker/start.sh"]
