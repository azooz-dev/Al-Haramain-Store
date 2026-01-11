#!/bin/bash
# ===========================================
# Al-Haramain Store - Railway Nginx Startup
# ===========================================
# Runs PHP-FPM + Nginx for production on Railway
# with frontend reverse proxy support
# ===========================================

set -e

echo "==========================================="
echo "🚀 Starting Al-Haramain Store (Nginx Mode)"
echo "==========================================="

# Set default port if not set
PORT=${PORT:-80}
export PORT

# Check required directories
mkdir -p /var/log/nginx /var/log/php /tmp/nginx storage/logs storage/framework/cache storage/framework/sessions storage/framework/views bootstrap/cache

# Set permissions
chmod -R 775 storage bootstrap/cache

# Wait for database (if configured)
if [ -n "$DB_HOST" ]; then
    echo "⏳ Waiting for database..."
    max_tries=30
    tries=0
    
    while [ $tries -lt $max_tries ]; do
        if php artisan db:monitor --databases=mysql 2>/dev/null; then
            echo "✅ Database connected!"
            break
        fi
        tries=$((tries + 1))
        echo "   Attempt $tries/$max_tries..."
        sleep 2
    done
fi

# Run migrations if enabled
if [ "$AUTO_MIGRATE" = "true" ]; then
    echo "🔄 Running migrations..."
    php artisan migrate --force --no-interaction
fi

# Create storage link
if [ ! -L public/storage ]; then
    echo "🔗 Creating storage link..."
    php artisan storage:link
fi

# Cache configuration for production
echo "🔧 Caching configuration..."
php artisan config:cache
php artisan route:cache
php artisan view:cache || true
php artisan event:cache || true

# ===========================================
# Configure Nginx
# ===========================================
echo "🌐 Configuring Nginx..."

# Create nginx config directory if it doesn't exist
mkdir -p /tmp/nginx

# Check for frontend proxy configuration
if [ -n "$FRONTEND_UPSTREAM_HOST" ]; then
    echo "   📡 Frontend proxy enabled: $FRONTEND_UPSTREAM_HOST"
    
    # Use production config with frontend proxy
    cat > /tmp/nginx/app.conf << 'NGINX_CONF'
server {
    listen ${PORT};
    server_name _;
    root /app/public;
    index index.php;
    charset utf-8;

    # Gzip
    gzip on;
    gzip_types text/plain text/css application/json application/javascript text/xml application/xml;

    # Health check
    location /health {
        access_log off;
        return 200 "healthy\n";
        add_header Content-Type text/plain;
    }

    # API Routes → Laravel
    location /api/ {
        try_files $uri $uri/ /index.php?$query_string;
    }

    # Sanctum → Laravel
    location /sanctum/ {
        try_files $uri $uri/ /index.php?$query_string;
    }

    # Admin Panel → Laravel
    location /admin {
        try_files $uri $uri/ /index.php?$query_string;
    }

    # Livewire → Laravel
    location /livewire {
        try_files $uri $uri/ /index.php?$query_string;
    }

    # Broadcasting → Laravel
    location /broadcasting/ {
        try_files $uri $uri/ /index.php?$query_string;
    }

    # Storage files
    location /storage/ {
        alias /app/storage/app/public/;
        expires 1M;
        add_header Cache-Control "public";
        add_header Access-Control-Allow-Origin "*";
        try_files $uri =404;
    }

    # Laravel build assets
    location /build/ {
        alias /app/public/build/;
        expires 1y;
        add_header Cache-Control "public, immutable";
        try_files $uri =404;
    }

    # Filament assets
    location /js/filament/ {
        alias /app/public/js/filament/;
        expires 1y;
        try_files $uri =404;
    }

    location /css/filament/ {
        alias /app/public/css/filament/;
        expires 1y;
        try_files $uri =404;
    }

    # PHP processing
    location ~ \.php$ {
        fastcgi_pass 127.0.0.1:9000;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }

    # Frontend proxy - everything else
    location / {
        proxy_pass https://${FRONTEND_UPSTREAM_HOST};
        proxy_ssl_server_name on;
        proxy_http_version 1.1;
        proxy_set_header Upgrade $http_upgrade;
        proxy_set_header Connection 'upgrade';
        proxy_set_header Host ${FRONTEND_UPSTREAM_HOST};
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
        proxy_cache_bypass $http_upgrade;
        proxy_connect_timeout 60s;
        proxy_send_timeout 60s;
        proxy_read_timeout 60s;
    }

    # Security
    location ~ /\.(?!well-known).* {
        deny all;
    }
}
NGINX_CONF

else
    echo "   ℹ️ Backend-only mode (no frontend proxy)"
    
    # Use basic config without frontend proxy
    cat > /tmp/nginx/app.conf << 'NGINX_CONF'
server {
    listen ${PORT};
    server_name _;
    root /app/public;
    index index.php;
    charset utf-8;

    gzip on;
    gzip_types text/plain text/css application/json application/javascript text/xml application/xml;

    location /health {
        access_log off;
        return 200 "healthy\n";
        add_header Content-Type text/plain;
    }

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass 127.0.0.1:9000;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
NGINX_CONF
fi

# Substitute environment variables in nginx config
envsubst '${PORT} ${FRONTEND_UPSTREAM_HOST}' < /tmp/nginx/app.conf > /tmp/nginx/default.conf

echo "✅ Nginx configured on port $PORT"

# ===========================================
# Start Services
# ===========================================
echo "🚀 Starting services..."

# Start PHP-FPM in background
php-fpm -D

# Start Nginx in foreground
echo "==========================================="
echo "✅ Al-Haramain Store is running!"
echo "   Port: $PORT"
if [ -n "$FRONTEND_UPSTREAM_HOST" ]; then
    echo "   Frontend: https://$FRONTEND_UPSTREAM_HOST"
fi
echo "==========================================="

# Run nginx with custom config
exec nginx -c /tmp/nginx/nginx.conf -g "daemon off;" || exec nginx -g "daemon off;"
