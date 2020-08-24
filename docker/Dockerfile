FROM justckr/ubuntu-nginx-php:php7.2

# install psr extension:
RUN pecl channel-update pecl.php.net && pecl install psr && \
    echo "extension = psr.so" > /etc/php/7.2/fpm/conf.d/10-psr.ini && \
    echo "extension = psr.so" > /etc/php/7.2/cli/conf.d/10-psr.ini;

# install redis php extension:
RUN pecl install redis && \
    echo "extension = redis.so" > /etc/php/7.2/fpm/conf.d/30-redis.ini && \
    echo "extension = redis.so" > /etc/php/7.2/cli/conf.d/30-redis.ini;

# install php phalcon
RUN curl -s https://packagecloud.io/install/repositories/phalcon/stable/script.deb.sh | bash
RUN apt-get update && apt-get install php7.2-phalcon -y

# install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

COPY phalcon-start.sh /

CMD ["/phalcon-start.sh"]
