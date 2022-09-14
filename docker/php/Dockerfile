FROM php:8.1-fpm

# Fix debconf warnings upon build
ARG DEBIAN_FRONTEND=noninteractive

ARG DOCKER_GROUP_ID
ARG DOCKER_USER_ID

USER root

RUN usermod -u ${DOCKER_USER_ID} -d /var/run/php www-data && \
    groupmod -g ${DOCKER_GROUP_ID} www-data

# Fix user permissions
RUN mkdir -p /var/run/php

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN chown www-data:www-data /usr/local/bin               && \
    chmod 755 /usr/local/bin

RUN apt-get update && apt-get install -y \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libpng-dev \
        zip  \
        unzip \
    && docker-php-ext-install mysqli pdo pdo_mysql exif

# Install selected extensions and other stuff
RUN apt-get update \
    && apt-get -y --no-install-recommends install php-memcached \
        php8.1-mysql php8.1-pgsql php8.1-pdo php8.1-pdo_mysql php-redis php8.1-sqlite3 php-xdebug \
        php8.1-bcmath php8.1-bz2 php8.1-dba \
        php8.1-enchant php8.1-gd php8.1-gmp php-igbinary php-imagick php8.1-imap \
        php8.1-intl php8.1-ldap php-mongodb php-msgpack php8.1-soap php-ssh2 \
        php8.1-sybase php-tideways php8.1-tidy php8.1-xmlrpc php8.1-xsl \
    && apt-get clean; rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/* \

USER www-data

COPY composer.json composer.json

RUN composer i \
   --no-interaction \
   --no-plugins \
   --no-scripts \
   --no-dev \
   --prefer-dist

COPY . .

# Fix user permissions
RUN chown -R www-data:www-data /var/run/php && \
    chmod 755 /var/run/php

RUN composer dump-autoload

RUN php artisan key:generate
RUN php artisan storage:link
#RUN php artisan migrate:fresh
#RUN php artisan passport:install --force
#RUN php artisan db:seed

CMD [ "php-fpm" ]
