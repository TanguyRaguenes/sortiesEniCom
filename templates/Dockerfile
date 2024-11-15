# Étape 1 : Image PHP avec PHP-FPM
FROM php:8.1-fpm as php

# Installer les extensions nécessaires
RUN docker-php-ext-install pdo pdo_mysql

# Copier les fichiers Symfony
WORKDIR /app
COPY . /app

# Étape 2 : Image Nginx
FROM nginx:latest

# Copier la configuration Nginx
COPY nginx.conf /etc/nginx/conf.d/default.conf

# Copier les fichiers Symfony
COPY --from=php /app /app

# Exposer le port 8080
EXPOSE 8080

# Démarrer Nginx
CMD ["nginx", "-g", "daemon off;"]