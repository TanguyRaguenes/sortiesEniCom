# Utiliser l'image PHP officielle pour Symfony avec la version spécifique 8.2.18
FROM php:8.2.18-fpm

# Installer les dépendances nécessaires pour Symfony
RUN apt-get update && apt-get install -y \
    libpng-dev libjpeg-dev libfreetype6-dev \
    zip git curl \
    libxml2-dev \
    libicu-dev \
    libzip-dev \
    libonig-dev \
    libssl-dev

# Installer Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Vérifier que Composer est bien installé
RUN composer --version

# Copier le contenu de ton application Symfony dans le conteneur
WORKDIR /var/www
COPY . .

# Donner les bonnes permissions pour le répertoire de travail
RUN chown -R www-data:www-data /var/www && chmod -R 755 /var/www

# Installer les dépendances sans exécuter les scripts (pour éviter le problème)
RUN composer install --no-scripts --ignore-platform-reqs --no-plugins --verbose

# Exposer le port utilisé par PHP-FPM
EXPOSE 9000

# Lancer PHP-FPM
CMD ["php-fpm"]