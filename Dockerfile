# Utiliser l'image PHP officielle pour Symfony
FROM php:8.2-fpm

# Installer les dépendances nécessaires pour Symfony (les extensions PHP de base)
RUN apt-get update && apt-get install -y \
    libpng-dev libjpeg-dev libfreetype6-dev zip git curl \
    && rm -rf /var/lib/apt/lists/*

# Installer Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Ajouter un utilisateur non-root pour Composer
RUN useradd -m appuser

# Définir le répertoire de travail et copier l'application Symfony
WORKDIR /var/www
COPY . /var/www

# Donner les droits d’écriture à appuser sur /var/www
RUN chown -R appuser:appuser /var/www

# Passer à l’utilisateur non-root pour installer les dépendances
USER appuser
RUN composer install --no-scripts --ignore-platform-reqs

# Repasse à root pour exécuter des commandes Symfony nécessitant des droits élevés
USER root
RUN php bin/console cache:clear
RUN php bin/console assets:install public

# Revenir à l’utilisateur non-root pour la sécurité
USER appuser

# Exposer le port utilisé par Railway
EXPOSE 80

# Lancer PHP-FPM sans démonisation pour que Docker puisse le gérer
CMD ["php-fpm", "--fpm-config", "/usr/local/etc/php-fpm.conf", "--nodaemonize"]