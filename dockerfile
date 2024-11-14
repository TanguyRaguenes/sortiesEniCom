# Utiliser l'image PHP officielle pour Symfony
FROM php:8.2-fpm

# Installer les dépendances nécessaires pour Symfony
RUN apt-get update && apt-get install -y libpng-dev libjpeg-dev libfreetype6-dev zip git curl

# Installer Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Copier le contenu de ton application Symfony dans le conteneur
WORKDIR /var/www
COPY . .

# Installer les dépendances sans exécuter les scripts (pour éviter le problème)
RUN composer install --no-scripts --ignore-platform-reqs

# Exposer le port utilisé par PHP-FPM
EXPOSE 9000

# Lancer PHP-FPM
CMD ["php-fpm"]