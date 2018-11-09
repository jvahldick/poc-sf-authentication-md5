FROM nginx:1.13-alpine

### Copying NGINX config files
COPY docker/nginx/conf/nginx.conf /etc/nginx/nginx.conf

# Production
COPY docker/nginx/sites/app.conf /etc/nginx/conf.d/default.conf

RUN apk update \
    && apk upgrade \
    && apk add --no-cache bash \
    && adduser -D -H -u 1000 -s /bin/bash www-data

ARG PHP_UPSTREAM_CONTAINER=php-fpm
ARG PHP_UPSTREAM_PORT=9000

# Set upstream conf and remove the default conf
RUN echo "upstream php-upstream { server ${PHP_UPSTREAM_CONTAINER}:${PHP_UPSTREAM_PORT}; }" \
    > /etc/nginx/conf.d/upstream.conf \
    && mkdir -p /var/www/app

COPY ./app/public/ /var/www/app/public

CMD ["nginx"]
