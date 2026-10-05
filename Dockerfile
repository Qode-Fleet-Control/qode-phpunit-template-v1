# Built by .github/workflows/deploy.yml (context ., file Dockerfile) and pushed
# to Artifact Registry.
#
# A job image, not a server: the default command runs the PHPUnit suite and exits
# 0 when it passes. php.ini-development is enabled because the generated phpunit.xml
# sets warnWhenPhpIsNotConfiguredForDevelopment (and failOnWarning).
FROM php:8.4-cli-alpine AS runtime
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
RUN cp "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini" \
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
