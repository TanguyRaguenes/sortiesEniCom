# Utiliser l'image PHP officielle pour Symfony avec PHP-FPM
FROM php:8.2-fpm

# Installer les dépendances nécessaires pour Symfony et Nginx
RUN apt-get update && apt-get install -y \
    libpng-dev libjpeg-dev libfreetype6-dev zip git curl nginx \
    && rm -rf /var/lib/apt/lists/*

# Installer Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Ajouter un utilisateur non-root pour Composer
RUN useradd -m appuser

# Définir le répertoire de travail et copier l'application Symfony
WORKDIR /var/www
COPY . /var/www

# Créer le dossier var pour Symfony et donner les droits d’écriture à appuser
RUN mkdir -p /var/www/var && chown -R appuser:appuser /var/www/var

# Passer à l’utilisateur non-root pour installer les dépendances
USER appuser
RUN composer install --no-scripts --ignore-platform-reqs

# Revenir à root pour configurer Nginx
USER root
COPY nginx.conf /etc/nginx/nginx.conf
RUN ln -s /var/www/public /var/www/html  # Redirige le root Nginx vers le dossier public de Symfony

# Effacer le cache Symfony pour l’environnement de production
RUN php bin/console cache:clear --env=prod

# Exposer le port utilisé par Railway
EXPOSE 80

# Lancer Nginx et PHP-FPM ensemble
CMD service nginx start && php-fpm --nodaemonize
