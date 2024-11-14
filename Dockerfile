# Utiliser l'image PHP officielle en ligne de commande pour Symfony
FROM php:8.2-cli

# Installer les dépendances nécessaires pour Symfony
RUN apt-get update && apt-get install -y \
    libpng-dev libjpeg-dev libfreetype6-dev zip git curl \
    && rm -rf /var/lib/apt/lists/*

# Installer Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Définir le répertoire de travail et copier l'application Symfony
WORKDIR /app
COPY . /app

# Installer les dépendances Composer
RUN composer install --no-scripts --ignore-platform-reqs

# Effacer le cache Symfony pour l’environnement de production
RUN php bin/console cache:clear --env=prod

# Exposer le port utilisé par le serveur Symfony
EXPOSE 80

# Lancer le serveur Symfony en mode avant-plan
CMD php -S 0.0.0.0:80 -t public