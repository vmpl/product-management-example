FROM node:latest AS node

FROM php:8.1-fpm AS fpm

ARG COMPOSER_VERSION=2

COPY --from=node /usr/local/lib/node_modules /usr/local/lib/node_modules
COPY --from=node /usr/local/bin/node /usr/local/bin/node
RUN ln -s /usr/local/lib/node_modules/npm/bin/npm-cli.js /usr/local/bin/npm \
    && npm config set cache /var/www/html/.npm --global

RUN usermod -d /var/www/html www-data

RUN apt-get update \
    && apt-get upgrade -y \
    && apt-get install -y git zip unzip libzip-dev libxml2 libxml2-dev libxslt-dev libsodium-dev libbz2-dev msmtp \
    && docker-php-ext-install zip xsl sodium bz2

RUN apt-get install -y \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libpng-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd

RUN docker-php-ext-install mysqli pdo pdo_mysql soap opcache exif intl bcmath sockets
RUN docker-php-ext-install calendar gettext pcntl sysvmsg sysvsem sysvshm
RUN pecl install redis \
    && docker-php-ext-enable redis

RUN apt install -y libjudy-dev \
    && pecl install memprof \
    && docker-php-ext-enable memprof \
    && echo "memprof.output_dir=/app/.ignore/memprof" >> /usr/local/etc/php/conf.d/docker-php-ext-memprof.ini

RUN echo "opcache.memory_consumption=256" >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini \
    && echo "opcache.interned_strings_buffer=16" >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini \
    && echo "opcache.revalidate_freq=0" >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini \
    && echo "opcache.max_accelerated_files=16229" >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini

# Install composer
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
RUN if [ `echo "$COMPOSER_VERSION" | grep -o "[0-9]\+" | wc -l` -eq 3 ]; then \
        php composer-setup.php --version=$COMPOSER_VERSION; \
    else \
        php composer-setup.php --$COMPOSER_VERSION; \
    fi
RUN rm composer-setup.php && \
    chmod +x composer.phar && \
    mv composer.phar /usr/local/bin/composer

USER www-data
WORKDIR /var/www/html

FROM fpm AS fpm-debug

USER root

RUN pecl install xdebug \
    && docker-php-ext-enable xdebug

RUN echo "xdebug.mode=debug" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.idekey=PHPSTORM" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.start_with_request=on" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.trigger_value=PHPSTORM" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.client_host=host.docker.internal" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.client_port=9000" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

USER www-data

FROM fpm-debug AS cli

USER root

RUN echo "xdebug.mode=develop" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.idekey=PHPSTORM" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.start_with_request=on" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.trigger_value=PHPSTORM" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.client_host=host.docker.internal" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.client_port=9000" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

USER www-data

FROM cli as cron

USER root

RUN apt-get update \
    && apt-get install -y cron \
    && mkdir -p /var/log/cron && touch /var/log/cron/cron.log \
    && mkdir -p /tmp/magento/var/log && touch /tmp/magento/var/log/cron.log

RUN echo "* * * * * www-data /usr/local/bin/php /var/www/html/bin/magento cron:run | grep -v \"Ran jobs by schedule\" >> /var/www/html/var/log/magento.cron.log" > /etc/cron.d/crontab \
    && echo "# An empty line is required at the end of this file for a valid cron file." >> /etc/cron.d/crontab \
    && chmod 0644 /etc/cron.d/crontab \
    && crontab /etc/cron.d/crontab

CMD cron -f && tail -f /app/var/log/cron.log
