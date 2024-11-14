# Utiliser l'image PHP avec PHP-FPM
FROM php:8.2-fpm

# Installer les dépendances nécessaires (Nginx et PHP)
RUN apt-get update && apt-get install -y \
    libpng-dev libjpeg-dev libfreetype6-dev zip git curl nginx \
    && rm -rf /var/lib/apt/lists/*

# Installer Composer pour gérer les dépendances PHP
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Définir le répertoire de travail
WORKDIR /var/www
COPY . /var/www

# Copier le fichier nginx.conf dans le conteneur
COPY nginx.conf /etc/nginx/nginx.conf

# Installer les dépendances Composer
RUN composer install --no-scripts --ignore-platform-reqs

# Créer les répertoires nécessaires pour Nginx
RUN mkdir -p /var/log/nginx && chown -R www-data:www-data /var/log/nginx

# Effacer le cache Symfony pour la production
RUN php bin/console cache:clear --env=prod

# Exposer le port 80 pour Nginx
EXPOSE 80

# Démarrer Nginx et PHP-FPM
CMD service nginx start && php-fpm --nodaemonize
