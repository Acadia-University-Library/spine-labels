FROM php:8.4-apache

COPY js /var/www/html/js
COPY css /var/www/html/css
COPY images /var/www/html/images
COPY spinelabels.php /var/www/html/spinelabels.php
COPY index.php /var/www/html/index.php
RUN mkdir /var/www/html/configs

# install production php ini
RUN cp /usr/local/etc/php/php.ini-production /usr/local/etc/php/php.ini

# symlink secret into config file
RUN ln -s /run/secrets/config /var/www/html/configs/config.php
