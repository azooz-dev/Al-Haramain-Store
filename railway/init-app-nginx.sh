#!/bin/bash
# ============================================
# Al-Haramain Store - Railway App Initialization (Nginx + PHP-FPM)
# Runs during deployment to set up the application
# Use this script when you need the reverse proxy to frontend
# ============================================

set -e

echo "==========================================="
echo "🚀 Initializing Al-Haramain Store (Nginx Mode)..."
echo "==========================================="

# Run database migrations
echo "📦 Running database migrations..."
php artisan migrate --force

# Run database seeders (only if database is empty)
echo "🌱 Seeding database..."
php artisan db:seed --force || true

# Publish Filament assets
echo "🎨 Publishing Filament assets..."
php artisan filament:assets

# Clear all caches first
echo "🧹 Clearing caches..."
php artisan optimize:clear

# Cache configuration for production
echo "⚡ Caching configuration..."
php artisan config:cache
php artisan event:cache
php artisan route:cache
php artisan view:cache

# Create storage link if not exists
echo "🔗 Creating storage link..."
php artisan storage:link || true

echo "==========================================="
echo "✅ Al-Haramain Store initialization complete!"
echo "==========================================="

# ===========================================
# Generate Nginx config from template
# ===========================================
echo "🔧 Generating Nginx configuration..."

# Default values
PORT="${PORT:-8080}"
FRONTEND_UPSTREAM_HOST="${FRONTEND_UPSTREAM_HOST:-localhost}"

# Export for envsubst
export PORT
export FRONTEND_UPSTREAM_HOST

# Check if nginx config template exists
NGINX_TEMPLATE="/var/www/html/docker/nginx/sites/production.conf"
NGINX_CONFIG="/etc/nginx/sites-enabled/default"

if [ -f "$NGINX_TEMPLATE" ]; then
    echo "📝 Processing Nginx template..."
    envsubst '${PORT} ${FRONTEND_UPSTREAM_HOST}' < "$NGINX_TEMPLATE" > "$NGINX_CONFIG"
    echo "✅ Nginx config generated at $NGINX_CONFIG"
else
    echo "⚠️ Nginx template not found at $NGINX_TEMPLATE"
    echo "Using PHP built-in server instead..."
    exec php -S 0.0.0.0:${PORT} -t public public/server.php
fi

# ===========================================
# Start PHP-FPM and Nginx
# ===========================================
echo "🌐 Starting PHP-FPM..."
php-fpm -D

echo "🌐 Starting Nginx on port ${PORT}..."
echo "📡 Frontend proxy: ${FRONTEND_UPSTREAM_HOST}"

# Start Nginx in foreground
exec nginx -g "daemon off;"
