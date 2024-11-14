# Utiliser l'image PHP officielle avec PHP-FPM
FROM php:8.2-fpm

# Installer les dépendances nécessaires
RUN apt-get update && apt-get install -y \
    libpng-dev libjpeg-dev libfreetype6-dev zip git curl nginx \
    && rm -rf /var/lib/apt/lists/*

# Installer Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Copier l'application Symfony
WORKDIR /var/www
COPY . /var/www

# Donner les droits nécessaires
RUN chown -R www-data:www-data /var/www

# Installer les dépendances Composer
RUN composer install --no-scripts --ignore-platform-reqs

# Copier la configuration Nginx
COPY nginx.conf /etc/nginx/nginx.conf

# Créer les répertoires de logs
RUN mkdir -p /var/log/nginx && chown -R www-data:www-data /var/log/nginx

# Effacer le cache Symfony pour la production
RUN php bin/console cache:clear --env=prod

# Exposer le port 80
EXPOSE 80

# Lancer Nginx et PHP-FPM
CMD service nginx start && php-fpm --nodaemonize