#!/bin/sh
# ============================================
# Al-Haramain Store - Container Entrypoint
# Runs initialization tasks before starting app
# ============================================

set -e

echo "=========================================="
echo "🚀 Starting Al-Haramain Store..."
echo "=========================================="

# Create log directories
echo "📁 Creating log directories..."
mkdir -p /var/log/php /var/log/supervisor /var/log/nginx
chown -R www-data:www-data /var/log/php

# Wait for MySQL to be ready
if [ -n "$DB_HOST" ]; then
    echo "⏳ Waiting for database connection..."
    max_tries=30
    tries=0
    
    while [ $tries -lt $max_tries ]; do
        if php artisan db:monitor --databases=mysql 2>/dev/null; then
            echo "✅ Database connected!"
            break
        fi
        
        tries=$((tries + 1))
        echo "Database not ready, waiting... (attempt $tries/$max_tries)"
        sleep 2
    done
    
    if [ $tries -eq $max_tries ]; then
        echo "❌ Could not connect to database after $max_tries attempts"
        exit 1
    fi
fi

# Note: Redis connectivity will be verified by PHP when the app runs
echo "📡 Redis will be checked by PHP..."

# Run migrations (if AUTO_MIGRATE is set)
if [ "$AUTO_MIGRATE" = "true" ]; then
    echo "🔄 Running database migrations..."
    php artisan migrate --force --no-interaction
fi

# Create storage link if not exists
if [ ! -L /var/www/html/public/storage ]; then
    echo "🔗 Creating storage symlink..."
    php artisan storage:link
fi

# Optimize application for production
echo "🔧 Optimizing application..."
php artisan config:cache
php artisan route:cache
php artisan view:cache || echo "⚠️ View cache had warnings (non-critical)"
php artisan event:cache || echo "⚠️ Event cache had warnings (non-critical)"

# Set permissions
echo "🔐 Setting permissions..."
chown -R www-data:www-data /var/www/html/storage
chown -R www-data:www-data /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage
chmod -R 775 /var/www/html/bootstrap/cache

echo "==========================================="
echo "✅ Al-Haramain Store is ready!"
echo "==========================================="

# ===========================================
# Configure Frontend Proxy (if enabled)
# ===========================================
if [ -n "$FRONTEND_UPSTREAM_HOST" ]; then
    echo "🔄 Configuring frontend proxy..."
    echo "   Frontend host: $FRONTEND_UPSTREAM_HOST"
    
    # Check if production.conf exists and has the placeholder
    NGINX_CONF="/etc/nginx/http.d/default.conf"
    PROD_TEMPLATE="/var/www/html/docker/nginx/sites/production.conf"
    
    if [ -f "$PROD_TEMPLATE" ]; then
        # Replace placeholder with actual host and copy to nginx
        sed "s/FRONTEND_UPSTREAM_HOST_PLACEHOLDER/$FRONTEND_UPSTREAM_HOST/g" "$PROD_TEMPLATE" > "$NGINX_CONF"
        echo "✅ Frontend proxy configured at $NGINX_CONF"
    else
        echo "⚠️ Production nginx template not found, using default config"
    fi
else
    echo "ℹ️ FRONTEND_UPSTREAM_HOST not set - running backend-only mode"
fi

# Execute CMD (supervisord)
exec "$@"
