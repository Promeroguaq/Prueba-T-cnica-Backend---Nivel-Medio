# Usar una imagen base oficial de PHP con extensiones necesarias
FROM php:8.1-fpm

# Instalar dependencias del sistema (como herramientas y bibliotecas necesarias)
RUN apt-get update && apt-get install -y libpng-dev libjpeg-dev libfreetype6-dev libzip-dev git unzip libonig-dev

# Habilitar las extensiones de PHP necesarias para Laravel
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql zip

# Instalar Composer (gestor de dependencias de PHP)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Establecer el directorio de trabajo dentro del contenedor
WORKDIR /var/www

# Copiar el contenido de tu proyecto al contenedor
COPY . .

# Establecer los permisos para las carpetas de Laravel
RUN chown -R www-data:www-data /var/www
RUN chmod -R 775 /var/www/storage /var/www/bootstrap/cache

# Exponer el puerto en el que el servidor de PHP-FPM correrá
EXPOSE 9000

# Comando para iniciar PHP-FPM
CMD ["php-fpm"]
