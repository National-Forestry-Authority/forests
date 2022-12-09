# Based on https://github.com/docker-library/drupal/blob/0bc2672/9.4/php8.1/apache-bullseye/Dockerfile
# from https://www.drupal.org/docs/system-requirements/php-requirements
FROM php:7.4-apache-bullseye

SHELL ["/bin/bash", "-o", "pipefail", "-c"]
# install the PHP extensions we need
RUN set -eux; \
    \
    if command -v a2enmod; then \
        a2enmod rewrite; \
    fi; \
    \
    savedAptMark="$(apt-mark showmanual)"; \
    \
    apt-get update; \
    apt-get install -y --no-install-recommends \
        libfreetype6-dev \
        libjpeg-dev \
        libpng-dev \
        libpq-dev \
        libwebp-dev \
        libzip-dev \
    ; \
    \
    docker-php-ext-configure gd \
        --with-freetype \
        --with-jpeg=/usr \
        --with-webp \
    ; \
    \
    docker-php-ext-install -j "$(nproc)" \
        gd \
        opcache \
        pdo_mysql \
        pdo_pgsql \
        zip \
    ; \
    \
# reset apt-mark's "manual" list so that "purge --auto-remove" will remove all build dependencies
    apt-mark auto '.*' > /dev/null; \
    apt-mark manual $savedAptMark; \
    ldd "$(php -r 'echo ini_get("extension_dir");')"/*.so \
        | awk '/=>/ { print $3 }' \
        | sort -u \
        | xargs -r dpkg-query -S \
        | cut -d: -f1 \
        | sort -u \
        | xargs -rt apt-mark manual; \
    \
    apt-get purge -y --auto-remove -o APT::AutoRemove::RecommendsImportant=false; \
    rm -rf /var/lib/apt/lists/*

# Add needed extensions
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/

RUN install-php-extensions bcmath geos

# set recommended PHP.ini settings
# see https://secure.php.net/manual/en/opcache.installation.php
RUN { \
        echo 'opcache.memory_consumption=128'; \
        echo 'opcache.interned_strings_buffer=8'; \
        echo 'opcache.max_accelerated_files=4000'; \
        echo 'opcache.revalidate_freq=60'; \
        echo 'opcache.fast_shutdown=1'; \
    } > /usr/local/etc/php/conf.d/opcache-recommended.ini

COPY --from=composer:2 /usr/bin/composer /usr/local/bin/

COPY . /var/www

WORKDIR /var/www

RUN set -eux; \
    export COMPOSER_HOME="$(mktemp -d)"; \
    composer install --no-dev --optimize-autoloader; \
    rm -rf html; \
    ln -s web html; \
    #chown -R www-data:www-data web/sites web/modules web/themes; \
    rm -rf "$COMPOSER_HOME"

ENV PATH=${PATH}:/var/www/vendor/bin

RUN set -eux; \
    # Add entrypoint wrapper to ease drupal deployments
    mv docker/bin/custom-entrypoint /usr/local/bin/; \
    # Make apache pass env vars to PHP
    # See https://stackoverflow.com/a/63139773/1680927
    mv docker/apache/environment.conf /etc/apache2/conf-enabled/environment.conf; \
    # Load php.ini overrides
    mv docker/php/overrides.ini /usr/local/etc/php/conf.d/zz-overrides.ini; \
    # Remove settings.local.php if present
    rm -rf web/sites/default/settings.local.php; \
    # Load drupal settings fron env vars (also warks for drush)
    mv docker/drupal/settings.overrides.php web/sites/default/settings.overrides.php; \
    # Create working directories
    mkdir -p \
      web/sites/default/files \
      private \
      tmp \
      ; \
    chown -R www-data:www-data \
      web/sites/default/files \
      private \
      tmp \
      ; \
    chmod 2775 \
      web/sites/default/files \
      private \
      tmp \
      ;

# Initialize ENV vars for safety
ENV DB_HOST='mysql' \
    DB_PORT='3306' \
    DB_NAME='drupal' \
    DB_USER='drupal' \
    DB_PASS='drupal' \
    DB_PREFIX='' \
    DB_DRIVER='mysql' \
    PROJECT_BASE_URL='drupal' \
    TRUSTED_HOSTS='' \
    DEPLOY='1' \
    DEPLOY_CMD='sleep 10; drush deploy'

ENTRYPOINT ["custom-entrypoint"]
CMD ["apache2-foreground"]