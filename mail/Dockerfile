FROM php:7-fpm-alpine
RUN apk add --no-cache nginx
RUN mkdir /mail; chown 1000:1000 -R /mail
RUN docker-php-ext-install ctype
RUN docker-php-ext-install json
WORKDIR /mail
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer
COPY ["mail.php", "composer.json",  "/mail/"]
CMD composer install --no-dev --no-plugins --no-scripts --no-cache -n -o -v; php-fpm -D; nginx -c /mail/nginx.conf
