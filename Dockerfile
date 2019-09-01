FROM node:10

ADD . /var/www/symfony
WORKDIR /var/www/symfony
RUN yarn install --no-progress
RUN yarn build
RUN rm -rf node_modules && rm -rf assets

FROM php:7.2-fpm

MAINTAINER Michael Pollind <polli104@mail.chapman.edu>

RUN apt-get update && apt-get install -y \
    openssl \
    unzip \
    libpq-dev \
    libzip-dev \
    zip \
    nginx \
    supervisor

# Configure Ngnix ###############################
#ADD docker/nginx/nginx.conf /etc/nginx/
ADD docker/nginx/symfony-prod.conf /etc/nginx/sites-available/

RUN ln -s /etc/nginx/sites-available/symfony-prod.conf /etc/nginx/sites-enabled/symfony \
&& rm /etc/nginx/sites-enabled/default

RUN echo "upstream php-upstream { server 127.0.0.1:9000; }" > /etc/nginx/conf.d/upstream.conf

RUN usermod -u 1000 www-data
##################################################

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
&& composer --version

# Configure php extenions
RUN docker-php-ext-configure zip --with-libzip
RUN docker-php-ext-install pdo pdo_pgsql zip
RUN docker-php-ext-configure opcache --enable-opcache \
    && docker-php-ext-install opcache
RUN pecl install redis && docker-php-ext-enable redis

# bring in the symfony configuration from the first stage
COPY --from=0  --chown=www-data:www-data /var/www/symfony /var/www/symfony
WORKDIR /var/www/symfony

# PHP configurations for production

RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"
COPY docker/php7-fpm/opcache-prod.ini $PHP_INI_DIR/conf.d/opcache.ini

RUN sed -i '/realpath_cache_size/c \realpath_cache_size=4096K' "$PHP_INI_DIR/php.ini"
RUN sed -i '/realpath_cache_ttl/c \realpath_cache_ttl=600' "$PHP_INI_DIR/php.ini"


# ssh #############################################
ENV SSH_PASSWD "root:Docker!"
RUN apt-get update \
        && apt-get install -y --no-install-recommends dialog \
        && apt-get update \
	&& apt-get install -y --no-install-recommends openssh-server \
	&& apt-get install -y --no-install-recommends redis-server \
	&& echo "$SSH_PASSWD" | chpasswd

COPY sshd_config /etc/ssh/
####################################################

RUN composer install
RUN composer dump-autoload --optimize --classmap-authoritative

COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf

CMD ["/usr/bin/supervisord"]
EXPOSE 80
EXPOSE 443
EXPOSE 2222
