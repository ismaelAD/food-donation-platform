# Utiliser l'image PHP de base
FROM php:8.1-fpm

# Installation des dépendances PHP
RUN apt-get update && apt-get install -y libpng-dev libjpeg-dev libfreetype6-dev libzip-dev git unzip
RUN docker-php-ext-configure gd --with-freetype --with-jpeg
RUN docker-php-ext-install gd zip pdo pdo_mysql

# Installer Composer (gestionnaire de dépendances PHP)
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Installer Node.js et npm
RUN curl -sL https://deb.nodesource.com/setup_16.x | bash - && \
    apt-get install -y nodejs

# Définir le répertoire de travail
WORKDIR /var/www

# Copier les fichiers de l'application dans le conteneur
COPY . .

# Installer les dépendances PHP et JavaScript
RUN composer install --no-dev --optimize-autoloader
RUN npm install

# Compiler les assets front-end avec Vue.js
RUN npm run build

# Exposer le port 8080
EXPOSE 8080

# Démarrer l'application Laravel
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8080"]
