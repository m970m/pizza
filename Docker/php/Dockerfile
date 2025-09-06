FROM php:8.4-fpm

ARG USER_ID=1000
ARG GROUP_ID=1000

WORKDIR /var/www

RUN apt-get update && apt-get install -y \
    git \
    zip \
    unzip \
    curl

RUN groupadd --force --gid ${GROUP_ID} appuser && \
    useradd --no-log-init --uid ${USER_ID} --gid ${GROUP_ID} --shell /bin/bash --home /var/www appuser

RUN chown -R appuser:appuser /var/www

RUN docker-php-ext-install pdo pdo_mysql

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN pecl install xdebug

RUN pecl install redis \
    && docker-php-ext-enable redis

COPY php/conf.d/* $PHP_INI_DIR/conf.d

USER appuser

CMD ["php-fpm"]
