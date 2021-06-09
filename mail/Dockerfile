FROM php:7-fpm-alpine
RUN apk add --no-cache composer nginx
RUN mkdir /mail; chown 1000:1000 -R /mail
WORKDIR /mail
COPY ["mail.php", "composer.json",  "/mail/"]
CMD composer install --no-dev --no-plugins --no-scripts --no-cache -n -o -v; php-fpm -D; nginx -c /mail/nginx.conf
