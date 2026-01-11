# ===========================================
# Al-Haramain Store - Root Dockerfile
# This file exists in root for Railway to detect
# It references the actual Dockerfile in docker/php/
# ===========================================

# Include the actual production Dockerfile
# Using Docker's built-in include mechanism is not supported
# So we copy the content directly

# ============================================
# Stage 1: Composer Dependencies
# ============================================
FROM composer:2 AS composer

WORKDIR /app

# Copy composer files first (better caching)
COPY composer.json composer.lock ./

# Copy module composer files
COPY Modules/Catalog/composer.json ./Modules/Catalog/
COPY Modules/User/composer.json ./Modules/User/
COPY Modules/Review/composer.json ./Modules/Review/
COPY Modules/Order/composer.json ./Modules/Order/
COPY Modules/Payment/composer.json ./Modules/Payment/
COPY Modules/Coupon/composer.json ./Modules/Coupon/
COPY Modules/Offer/composer.json ./Modules/Offer/
COPY Modules/Favorite/composer.json ./Modules/Favorite/
COPY Modules/Admin/composer.json ./Modules/Admin/
COPY Modules/Auth/composer.json ./Modules/Auth/
COPY Modules/Analytics/composer.json ./Modules/Analytics/

# Install dependencies without dev packages
RUN composer install \
  --no-dev \
  --no-scripts \
  --no-autoloader \
  --prefer-dist \
  --ignore-platform-reqs

# Copy all source files
COPY . .

# Generate optimized autoloader
RUN composer dump-autoload --optimize --no-dev

# ============================================
# Stage 2: Frontend Assets (Node.js)
# ============================================
FROM node:20-alpine AS frontend

WORKDIR /app

# Copy package files
COPY package*.json ./
COPY vite.config.js ./
COPY vite-module-loader.js ./

# Install ALL dependencies (including devDependencies for build tools like Vite)
RUN npm ci

# Copy source files needed for build
COPY resources ./resources
COPY Modules ./Modules
COPY public ./public

# Build production assets
RUN npm run build

# ============================================
# Stage 3: Production Image (PHP-FPM + Nginx)
# ============================================
FROM php:8.4-fpm-alpine AS production

LABEL maintainer="Dv.Abdulaziz Alameri"
LABEL description="Al-Haramain Store Production Image"

# Set environment
ENV APP_ENV=production
ENV APP_DEBUG=false

# Install system dependencies
RUN apk update && apk add --no-cache \
  nginx \
  bash \
  supervisor \
  curl \
  zip \
  unzip \
  git \
  libpng-dev \
  libjpeg-turbo-dev \
  freetype-dev \
  libzip-dev \
  icu-dev \
  oniguruma-dev \
  linux-headers \
  gettext \
  $PHPIZE_DEPS

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
  && docker-php-ext-configure pdo_mysql --with-pdo-mysql=mysqlnd \
  && docker-php-ext-configure mysqli --with-mysqli=mysqlnd \
  && docker-php-ext-install -j$(nproc) \
  pdo_mysql \
  mysqli \
  gd \
  zip \
  intl \
  mbstring \
  opcache \
  bcmath \
  pcntl \
  && pecl install redis \
  && docker-php-ext-enable redis

# Clean up build dependencies
RUN apk del $PHPIZE_DEPS linux-headers \
  && rm -rf /var/cache/apk/* \
  && rm -rf /tmp/*

# Set working directory
WORKDIR /var/www/html

# Create necessary directories
RUN mkdir -p \
  /var/log/php \
  /var/log/supervisor \
  /var/log/nginx \
  /run/nginx

# Copy configuration files
COPY docker/php/php.ini /usr/local/etc/php/php.ini
COPY docker/php/www.conf /usr/local/etc/php-fpm.d/www.conf
COPY docker/nginx/nginx.conf /etc/nginx/nginx.conf
COPY docker/nginx/sites/default.conf /etc/nginx/http.d/default.conf
# Copy production config as template for frontend proxy mode
COPY docker/nginx/sites/production.conf /etc/nginx/http.d/production.conf.template
COPY docker/supervisor/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Copy application from build stages
COPY --from=composer /app/vendor ./vendor
COPY --from=frontend /app/public/build ./public/build
COPY . .

# Remove development files
RUN rm -rf \
  .git \
  .github \
  tests \
  docs \
  node_modules \
  .env.example \
  phpunit.xml \
  README.md

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
  && chmod -R 755 /var/www/html/storage \
  && chmod -R 755 /var/www/html/bootstrap/cache \
  && chown -R www-data:www-data /var/log/php

# Copy and setup entrypoint
COPY docker/scripts/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh && chmod +x railway/*.sh

# Expose HTTP port
EXPOSE 80

# Health check
HEALTHCHECK --interval=30s --timeout=3s --start-period=10s --retries=3 \
  CMD curl -f http://localhost/health || exit 1

# Entrypoint and command
ENTRYPOINT ["/entrypoint.sh"]
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
