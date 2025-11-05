FROM php:8.2-fpm

ARG UID=1000
ARG GID=1000

WORKDIR /var/www/html

# Install system dependencies and PHP extensions required by Laravel
RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        git \
        unzip \
        curl \
        libzip-dev \
        libpq-dev \
        libicu-dev \
        libonig-dev \
        libxml2-dev \
        zip \
    && docker-php-ext-install \
        bcmath \
        intl \
        opcache \
        pcntl \
        pdo_mysql \
        zip \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Install Composer globally
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Create a non-root user that matches the host UID/GID for smoother volume mounts
RUN groupadd --gid "${GID}" laravel \
    && useradd --uid "${UID}" --gid laravel --shell /bin/bash --create-home laravel \
    && usermod -a -G www-data laravel \
    && chown -R laravel:www-data /var/www/html

USER laravel

# Ensure storage and cache directories are writable
RUN mkdir -p storage bootstrap/cache \
    && chmod -R ug+rw storage bootstrap/cache

CMD ["php-fpm"]
