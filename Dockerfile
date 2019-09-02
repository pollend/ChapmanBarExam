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
	&& echo "$SSH_PASSWD" | chpasswd


# redis ##########################################

ENV REDIS_VERSION 5.0.5
ENV REDIS_DOWNLOAD_URL http://download.redis.io/releases/redis-5.0.5.tar.gz
ENV REDIS_DOWNLOAD_SHA 2139009799d21d8ff94fc40b7f36ac46699b9e1254086299f8d3b223ca54a375

RUN set -eux; \
	\
	savedAptMark="$(apt-mark showmanual)"; \
	apt-get update; \
	apt-get install -y --no-install-recommends \
		ca-certificates \
		wget \
		\
		gcc \
		libc6-dev \
		make \
	; \
	rm -rf /var/lib/apt/lists/*; \
	\
	wget -O redis.tar.gz "$REDIS_DOWNLOAD_URL"; \
	echo "$REDIS_DOWNLOAD_SHA *redis.tar.gz" | sha256sum -c -; \
	mkdir -p /usr/src/redis; \
	tar -xzf redis.tar.gz -C /usr/src/redis --strip-components=1; \
	rm redis.tar.gz; \
	\
# disable Redis protected mode [1] as it is unnecessary in context of Docker
# (ports are not automatically exposed when running inside Docker, but rather explicitly by specifying -p / -P)
# [1]: https://github.com/antirez/redis/commit/edd4d555df57dc84265fdfb4ef59a4678832f6da
	grep -q '^#define CONFIG_DEFAULT_PROTECTED_MODE 1$' /usr/src/redis/src/server.h; \
	sed -ri 's!^(#define CONFIG_DEFAULT_PROTECTED_MODE) 1$!\1 0!' /usr/src/redis/src/server.h; \
	grep -q '^#define CONFIG_DEFAULT_PROTECTED_MODE 0$' /usr/src/redis/src/server.h; \
# for future reference, we modify this directly in the source instead of just supplying a default configuration flag because apparently "if you specify any argument to redis-server, [it assumes] you are going to specify everything"
# see also https://github.com/docker-library/redis/issues/4#issuecomment-50780840
# (more exactly, this makes sure the default behavior of "save on SIGTERM" stays functional by default)
	\
	make -C /usr/src/redis -j "$(nproc)"; \
	make -C /usr/src/redis install; \
	\
# TODO https://github.com/antirez/redis/pull/3494 (deduplicate "redis-server" copies)
	serverMd5="$(md5sum /usr/local/bin/redis-server | cut -d' ' -f1)"; export serverMd5; \
	find /usr/local/bin/redis* -maxdepth 0 \
		-type f -not -name redis-server \
		-exec sh -eux -c ' \
			md5="$(md5sum "$1" | cut -d" " -f1)"; \
			test "$md5" = "$serverMd5"; \
		' -- '{}' ';' \
		-exec ln -svfT 'redis-server' '{}' ';' \
	; \
	\
	rm -r /usr/src/redis; \
	\
	apt-mark auto '.*' > /dev/null; \
	[ -z "$savedAptMark" ] || apt-mark manual $savedAptMark > /dev/null; \
	apt-get purge -y --auto-remove -o APT::AutoRemove::RecommendsImportant=false; \
	\
	redis-cli --version; \
	redis-server --version

###################################################################

COPY sshd_config /etc/ssh/
####################################################

COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf

USER www-data
RUN composer install
RUN composer dump-autoload --optimize --classmap-authoritative
USER root

CMD ["/usr/bin/supervisord"]
EXPOSE 80
EXPOSE 443
EXPOSE 2222
