# -----------------------
# 1) Build Frontend Assets
# -----------------------
FROM node:22 AS node-builder

WORKDIR /app

# Copy package files first (better caching)
COPY package*.json vite.config.js ./
COPY resources ./resources

RUN npm install
RUN npm run build

# -----------------------
# 2) PHP + Laravel
# -----------------------
FROM php:8.2-cli AS app

WORKDIR /app

# Install dependencies: PHP extensions + system libs
RUN apt-get update && apt-get install -y \
    git unzip libpng-dev libonig-dev libxml2-dev zip curl \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copy Laravel files
COPY . .

# Copy built assets from node-builder stage
COPY --from=node-builder /app/public/build ./public/build

# Install PHP dependencies (without dev packages)
RUN composer install --optimize-autoloader --no-dev

# Clear and cache configs/routes/views
RUN php artisan config:clear \
 && php artisan route:clear \
 && php artisan view:clear \
 && php artisan cache:clear \
 && php artisan config:cache \
 && php artisan route:cache \
 && php artisan view:cache

# Expose Railway’s required port
EXPOSE 8080

# Start Laravel
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8080"]
