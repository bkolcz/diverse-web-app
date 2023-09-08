FROM php:8.2-apache
RUN a2enmod rewrite
RUN apt update \
    && apt install -y libzip-dev git wget --no-install-recommends \
    && apt clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*
RUN wget https://getcomposer.org/download/2.6.2/composer.phar \ 
    && mv composer.phar /usr/bin/composer && chmod +x /usr/bin/composer

COPY docker/apache.conf /etc/apache2/sites-enabled/000-default.conf
COPY docker/entrypoint.sh /entrypoint.sh
COPY . /var/www

WORKDIR /var/www
CMD ["apache2-foreground"]
RUN ["chmod","+x","/entrypoint.sh"]
ENTRYPOINT [ "/entrypoint.sh" ]