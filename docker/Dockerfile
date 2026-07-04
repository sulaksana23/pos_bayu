FROM php:8.4-fpm-alpine

RUN sed -i 's/dl-cdn.alpinelinux.org/mirrors.ustc.edu.cn/g' /etc/apk/repositories \
    && apk add --no-cache \
        postgresql-dev \
        postgresql-client \
        zip \
        unzip \
        git \
        curl \
    && docker-php-ext-install pdo pdo_pgsql

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/pos

RUN mkdir -p \
        storage/framework/cache/data \
        storage/framework/sessions \
        storage/framework/views \
        storage/framework/testing \
        storage/logs \
        storage/app/public \
        bootstrap/cache \
    && find storage bootstrap/cache -type d -exec chmod 2775 {} \; \
    && find storage bootstrap/cache -type f -exec chmod 0664 {} \; \
    && chown -R www-data:www-data storage bootstrap/cache

# Suppress E_NOTICE (tempnam fallback) and enable output buffering
# so Laravel can send headers after content has started.
RUN echo "error_reporting = E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT" > /usr/local/etc/php/conf.d/pos-custom.ini \
    && echo "output_buffering = On" >> /usr/local/etc/php/conf.d/pos-custom.ini

EXPOSE 9000

CMD ["php-fpm"]
