# Utilise une image officielle PHP avec PHP-FPM
FROM php:8.1-fpm

# Installer les extensions nécessaires (comme pdo_mysql pour MySQL)
RUN docker-php-ext-install pdo pdo_mysql

# Copier les fichiers de l'application
WORKDIR /app
COPY . /app

# Démarrer PHP-FPM
CMD ["php-fpm"]