# Étape 1 : Image PHP avec PHP-FPM
FROM php:8.1-fpm as php

# Installer les extensions nécessaires
RUN docker-php-ext-install pdo pdo_mysql

# Copier les fichiers directement dans /var/www
WORKDIR /var/www
COPY . /var/www

# Étape 2 : Image Nginx pour servir PHP via FastCGI
FROM nginx:latest

# Copier la configuration personnalisée de Nginx
COPY nginx.conf /etc/nginx/conf.d/default.conf

# Copier les fichiers de l'application Symfony
COPY --from=php /var/www /var/www

# Exposer le port attendu par Railway
EXPOSE 8080

# Démarrer Nginx
CMD ["nginx", "-g", "daemon off;"]