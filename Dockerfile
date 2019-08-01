FROM node:10

ADD . /var/www/symfony
WORKDIR /var/www/symfony
RUN yarn install
RUN yarn build

FROM php:7.2-fpm

MAINTAINER Michael Pollind <maxence.poutord@gmail.com>

RUN apt-get update && apt-get install -y \
    openssl \
    unzip \
    libpq-dev \
    libzip-dev \
    zip \
    nodejs \
    nginx \
    supervisor

###############################

#ADD docker/nginx/nginx.conf /etc/nginx/
ADD docker/nginx/symfony-prod.conf /etc/nginx/sites-available/

RUN ln -s /etc/nginx/sites-available/symfony-prod.conf /etc/nginx/sites-enabled/symfony \
&& rm /etc/nginx/sites-enabled/default

RUN echo "upstream php-upstream { server 127.0.0.1:9000; }" > /etc/nginx/conf.d/upstream.conf

RUN usermod -u 1000 www-data


#############################


# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
&& composer --version

RUN docker-php-ext-configure zip --with-libzip
RUN docker-php-ext-install pdo pdo_pgsql zip
RUN docker-php-ext-configure opcache --enable-opcache \
    && docker-php-ext-install opcache



COPY --from=0  --chown=www-data:www-data /var/www/symfony /var/www/symfony
WORKDIR /var/www/symfony

# ssh #########################################################
ENV SSH_PASSWD "root:Docker!"
RUN apt-get update \
        && apt-get install -y --no-install-recommends dialog \
        && apt-get update \
	&& apt-get install -y --no-install-recommends openssh-server \
	&& echo "$SSH_PASSWD" | chpasswd

COPY sshd_config /etc/ssh/
#########################################################

RUN composer install
#RUN composer dump-autoload --optimize --no-dev --classmap-authoritative

COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf

CMD ["/usr/bin/supervisord"]
EXPOSE 80
EXPOSE 443
