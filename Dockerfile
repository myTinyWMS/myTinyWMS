FROM mytinywms/mytinywms-base:1.1

COPY . /data/www

WORKDIR /data/www

RUN set -e -x \
	&& mkdir -p /etc/nginx/ssl \
	&& cp docker/nginx/ssl/nginx.crt /etc/nginx/ssl/nginx.crt \
	&& cp docker/nginx/ssl/nginx.key /etc/nginx/ssl/nginx.key \
    && cp docker/nginx/nginx.conf /etc/nginx/nginx.conf \
    && cp docker/php-fpm/www.conf /usr/local/etc/php-fpm.d/www.conf \
    && cp docker/php-fpm/php.ini /usr/local/etc/php/php.ini \
    && cp docker/supervisor/supervisord.conf /etc/supervisor/supervisord.conf \
    && mkdir -p storage/framework/cache \
    && mkdir -p storage/framework/sessions \
    && mkdir -p storage/framework/views \
    && chmod -R 777 storage


# composer
RUN set -ex \
	&& DOCKER_BUILD=true composer install --no-dev --no-progress --no-suggest --prefer-dist --optimize-autoloader \
 	&& rm -rf /root/.composer/cache

# node / npm
RUN set -ex \
	&& npm install \
	&& npm run prod

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/supervisord.conf"]