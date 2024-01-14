FROM php:8.1-fpm

RUN apt-get update && apt-get install -y \
    libzip-dev \
    libonig-dev \
    unzip \
    libsqlite3-dev \
    && docker-php-ext-install zip pdo_mysql pdo_sqlite

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Copie o script de inicialização para o contêiner
COPY init.sh /var/www/html


# Dê permissões de execução ao script
RUN chmod +x /var/www/html/init.sh

COPY . /app

RUN chown -R www-data:www-data /app/storage

# Exponha a porta 80 para o servidor Nginx
# EXPOSE 9000

CMD ["/var/www/html/init.sh"]

