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

# Exposer le port utilisé par le serveur Symfony
EXPOSE 8000

# Lancer le serveur Symfony
CMD php -S 0.0.0.0:8000 -t public