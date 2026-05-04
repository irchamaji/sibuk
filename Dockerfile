FROM php:8.4-fpm-alpine

# Install system dependencies dan PHP extensions
RUN apk add --no-cache \
    postgresql-dev \
    libzip-dev \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    oniguruma-dev \
    nodejs \
    npm \
    git \
    unzip \
    curl \
    bash \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
        pdo \
        pdo_pgsql \
        pgsql \
        zip \
        gd \
        opcache \
        mbstring \
        exif \
        pcntl \
        bcmath

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copy composer files dulu untuk cache layer Docker
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist

# Copy package files dulu untuk cache layer npm
# npm ci jauh lebih cepat dari npm install (pakai lock file langsung)
COPY package.json package-lock.json ./
RUN npm ci --no-audit

# Copy semua file aplikasi
COPY . .

# Generate autoloader Laravel dan jalankan post-install scripts
RUN composer dump-autoload --optimize

# Build aset frontend (Vite + Tailwind + Vue)
RUN npm run build

# Set permissions untuk storage dan cache
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Copy entrypoint script
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 9000

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
