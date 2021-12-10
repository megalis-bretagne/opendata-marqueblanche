# Dockerfile
FROM php:7.4-apache-buster

ENV COMPOSER_ALLOW_SUPERUSER=1

# git, unzip & zip are for composer
RUN apt-get update -qq && \
    apt-get install -qy \
    git \
    gnupg \
    unzip \
    libcurl4-gnutls-dev \
    libxml2 \
    libxml2-dev \
    wget \
    vim \
    libapache2-mod-auth-openidc \
    zip && \
    curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer && \
    apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

#Extension Apache Solr
RUN wget https://pecl.php.net/get/solr-2.5.0.tgz && \
tar -xvf solr-2.5.0.tgz && \
cd solr-2.5.0 && \
 phpize && \
 ./configure && \
   make && \
   make install

# PHP Extensions
RUN docker-php-ext-install -j$(nproc) opcache pdo_mysql
COPY config/php.ini /usr/local/etc/php/conf.d/app.ini

# Apache
COPY config/apache.conf /etc/apache2/conf-available/z-app.conf
COPY config/auth_openidc.conf /etc/apache2/mods-enabled/


RUN a2enmod rewrite remoteip headers && \
  a2enconf z-app


WORKDIR /var/www/html
EXPOSE 80

RUN ln -s /private private
RUN ln -s /public/OpenData OpenData


ADD . /var/www/html
RUN /usr/local/bin/composer install --no-interaction
RUN /usr/local/bin/composer dump-autoload --optimize

COPY docker-entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

CMD ["docker-entrypoint.sh"]




