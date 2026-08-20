FROM php:8.3-fpm

# Instalar dependencias del sistema
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    zip \
    libpq-dev

# Instalar extensión PDO para PostgreSQL
RUN docker-php-ext-install pdo pdo_pgsql

# Instalar Composer desde la imagen oficial
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# No mostrar errores/warnings de PHP en la respuesta HTTP; van al log
COPY docker/php/production.ini /usr/local/etc/php/conf.d/production.ini

# Directorio de trabajo
WORKDIR /var/www/html