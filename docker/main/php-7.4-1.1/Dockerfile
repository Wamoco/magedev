FROM php:7.4.16-apache

RUN apt-get update
RUN apt-get install -y gnupg2

RUN echo "deb http://ftp.de.debian.org/debian buster main" > /etc/apt/sources.list.d/zlibdev.list
RUN echo "deb http://repo.mysql.com/apt/debian/ buster mysql-8.0" > /etc/apt/sources.list.d/mysql.list

RUN apt-key adv --keyserver ha.pool.sks-keyservers.net --recv-keys 8C718D3B5072E1F5
RUN apt-get update

RUN curl -sL https://deb.nodesource.com/setup_15.x | bash -
RUN apt-get install -y nodejs
RUN npm install -g npm@7.8.0
RUN npm install -g grunt

RUN apt-get install -y git
RUN apt-get install -y mysql-community-client

RUN apt-get install -y libfreetype6-dev libjpeg62-turbo-dev libmcrypt-dev libpng-dev
RUN apt-get install -y libicu-dev
RUN apt-get install -y zlib1g-dev
RUN apt-get install -y libzip-dev
RUN apt-get install -y libxslt1.1 libxslt1-dev
RUN apt-get install -y libmagickwand-dev --no-install-recommends
RUN apt-get install -y libcurl4-openssl-dev


RUN docker-php-ext-install -j$(nproc) bcmath
RUN docker-php-ext-install -j$(nproc) ctype
RUN docker-php-ext-install -j$(nproc) curl
RUN docker-php-ext-install -j$(nproc) dom
RUN docker-php-ext-configure gd --with-freetype --with-jpeg
RUN docker-php-ext-install -j$(nproc) gd

#RUN docker-php-ext-install -j$(nproc) hash
RUN docker-php-ext-install -j$(nproc) iconv
RUN docker-php-ext-install -j$(nproc) intl

RUN apt-get install -y libonig-dev
RUN docker-php-ext-install -j$(nproc) mbstring
RUN docker-php-ext-install -j$(nproc) pdo_mysql
RUN docker-php-ext-install -j$(nproc) simplexml
RUN docker-php-ext-install -j$(nproc) soap
RUN docker-php-ext-install -j$(nproc) xsl
RUN docker-php-ext-install -j$(nproc) zip
RUN docker-php-ext-install -j$(nproc) sockets

RUN docker-php-ext-enable bcmath gd intl pdo_mysql simplexml soap xsl zip sockets

RUN pear upgrade --force
RUN pecl upgrade
RUN pecl install xdebug
RUN pecl install imagick

RUN a2enmod rewrite

RUN mkdir /var/www/.composer
RUN mkdir /var/www/.ssh
RUN mkdir /var/www/modules
RUN mkdir /var/www/composer-cache

RUN chown www-data:www-data /var/www/html
RUN chown www-data:www-data /var/www/.composer
RUN chown www-data:www-data /var/www/.ssh
RUN chown www-data:www-data /var/www/modules

RUN curl -O https://getcomposer.org/composer-stable.phar
RUN mv composer-stable.phar /usr/bin/composer
RUN chmod 777 /usr/bin/composer
RUN chmod +x /usr/bin/composer

# In case somebody still needs it
RUN curl -O https://getcomposer.org/composer-1.phar
RUN mv composer-1.phar /usr/bin/composer-1
RUN chmod 777 /usr/bin/composer-1
RUN chmod +x /usr/bin/composer-1

