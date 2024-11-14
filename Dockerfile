# Utiliser l'image Symfony préconfigurée avec Nginx et PHP-FPM
FROM symfonycorp/symfony-php:8.2-nginx

# Définir le répertoire de travail et copier l'application Symfony dans le conteneur
WORKDIR /app
COPY . /app

# Installer les dépendances Composer
RUN composer install --no-scripts --ignore-platform-reqs

# Exposer le port 80 (utilisé par Nginx)
EXPOSE 80