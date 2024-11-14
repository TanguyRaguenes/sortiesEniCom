# Utiliser une image de base Nginx
FROM nginx:latest

# Copier la configuration de Nginx
COPY nginx.conf /etc/nginx/nginx.conf

# Copier les fichiers de l'application Symfony dans le répertoire Nginx
WORKDIR /var/www
COPY . /var/www

# Exposer le port 80
EXPOSE 80

# Démarrer Nginx
CMD ["nginx", "-g", "daemon off;"]
