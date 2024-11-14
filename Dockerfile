# Étape 1: Utiliser l'image PHP avec FPM (FastCGI Process Manager)
FROM php:8.2-fpm

# Étape 2: Installer les dépendances nécessaires pour PHP et Nginx
RUN apt-get update && apt-get install -y \
    libpng-dev libjpeg-dev libfreetype6-dev zip git curl nginx \
    && rm -rf /var/lib/apt/lists/*

# Étape 3: Installer Composer (gestionnaire de dépendances pour PHP)
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Étape 4: Copier l'application Symfony dans le conteneur
WORKDIR /var/www
COPY . /var/www

# Étape 5: Donner les permissions nécessaires à l'utilisateur non-root
RUN chown -R www-data:www-data /var/www

# Étape 6: Installer les dépendances PHP de Symfony
RUN composer install --no-scripts --ignore-platform-reqs

# Étape 7: Copier la configuration Nginx
COPY nginx.conf /etc/nginx/nginx.conf

# Étape 8: Créer les répertoires nécessaires pour Nginx
RUN mkdir -p /var/log/nginx && chown -R www-data:www-data /var/log/nginx

# Étape 9: Effacer le cache Symfony pour la production
RUN php bin/console cache:clear --env=prod

# Étape 10: Exposer le port 80 pour que Nginx soit accessible
EXPOSE 80

# Étape 11: Lancer Nginx et PHP-FPM ensemble
CMD service nginx start && php-fpm --nodaemonize