FROM composer:1.7
FROM php:7.2-fpm-alpine

ENV APCU_VERSION 5.1.9
ENV COMPOSER_ALLOW_SUPERUSER 1

RUN apk upgrade -q -U -a \
    && apk add --no-cache --virtual .persistent-deps \
		git \
		icu-libs \
		gettext \
		bash \
        openssh \
        postgresql-dev

RUN set -xe \
	&& apk add --no-cache --virtual .build-deps \
		$PHPIZE_DEPS \
		icu-dev \
		freetype-dev \
    # PHP extensions
	&& docker-php-ext-install \
		pdo \
		mbstring \
		pdo_mysql \
		pdo_pgsql \
		intl \
    # APC and enabling PHP extensions
	&& pecl install apcu-${APCU_VERSION} \
	&& docker-php-ext-enable --ini-name 20-apcu.ini apcu \
	&& docker-php-ext-enable --ini-name 05-opcache.ini opcache

COPY ./docker/php-fpm/conf/ext/opcache.ini /usr/local/etc/php/conf.d/05-opcache.ini

COPY --from=0 /usr/bin/composer /usr/bin/composer

COPY ./docker/php-fpm/conf/php.prod.ini /usr/local/etc/php/php.ini

WORKDIR /var/www/app
