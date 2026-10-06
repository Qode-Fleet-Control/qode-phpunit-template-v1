# Built by .github/workflows/deploy.yml (context ., file Dockerfile) and pushed
# to Artifact Registry.
#
# A job image, not a server: the default command runs the PHPUnit suite and exits
# 0 when it passes. php.ini-development is enabled because the generated phpunit.xml
# sets warnWhenPhpIsNotConfiguredForDevelopment (and failOnWarning).
FROM php:8.4-cli-alpine AS runtime
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
# PHPUnit 13 checks that PHP is configured for development and turns a mismatch into a
# runner warning, which fails the run: php.ini-development still caps memory at 128M.
RUN cp "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini" \
 && echo "memory_limit=-1" > "$PHP_INI_DIR/conf.d/zz-phpunit.ini" \
 && adduser -D -u 10001 -h /app app
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-scripts --no-autoloader --prefer-dist --no-interaction
COPY . .
RUN composer dump-autoload --no-interaction && chown -R app:app /app
ARG BUILD_ID=""
ENV BUILD_ID=$BUILD_ID
USER app
CMD ["vendor/bin/phpunit"]
