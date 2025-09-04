# Utiliser l'image PHP de base
FROM php:8.2-fpm
RUN php -v

# Installation des dépendances PHP
RUN apt-get update && apt-get install -y \
    libpng-dev libjpeg-dev libfreetype6-dev libzip-dev git unzip curl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd zip pdo pdo_mysql

# Installer Composer (gestionnaire de dépendances PHP)
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Installer Node.js 20 et npm
RUN curl -sL https://deb.nodesource.com/setup_20.x | bash - && \
    apt-get install -y nodejs

# Définir le répertoire de travail
WORKDIR /var/www/html

# Copier les fichiers de l'application dans le conteneur
COPY . .

# Installer les dépendances PHP et JavaScript
RUN composer install --no-dev --optimize-autoloader
RUN npm install

# Compiler les assets front-end (Vite / Vue.js)
RUN npm run build

# Donner les bons droits à Laravel
RUN chown -R www-data:www-data storage bootstrap/cache

# Exposer le port 8080
EXPOSE 8000

# Lancer les migrations puis démarrer Laravel
CMD php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=8000