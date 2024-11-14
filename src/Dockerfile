# Utiliser l'image PHP officielle pour Symfony
FROM php:8.2-fpm

# Installer les dépendances nécessaires pour Symfony (les extensions PHP de base)
RUN apt-get update && apt-get install -y \
    libpng-dev libjpeg-dev libfreetype6-dev zip git curl \
    && rm -rf /var/lib/apt/lists/*

# Installer Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Ajouter un utilisateur non-root pour éviter les problèmes de permissions avec Composer
RUN useradd -m appuser

# Passer à l’utilisateur non-root
USER appuser

# Définir le répertoire de travail et copier l'application Symfony dans le conteneur
WORKDIR /var/www
COPY . /var/www

# Installer les dépendances Symfony (sans exécuter de scripts)
RUN composer install --no-scripts --ignore-platform-reqs

# Passer à root pour exécuter les commandes Symfony en tant qu’administrateur
USER root
RUN php bin/console cache:clear
RUN php bin/console assets:install public

# Exposer le port utilisé par Railway
EXPOSE 80

# Lancer PHP-FPM sur le port 80
CMD ["php-fpm", "-D", "listen=0.0.0.0:80"]