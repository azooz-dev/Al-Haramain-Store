#!/bin/bash
# ============================================
# Al-Haramain Store - Railway App Initialization
# Runs during deployment to set up the application
# ============================================

set -e

echo "==========================================="
echo "🚀 Initializing Al-Haramain Store..."
echo "==========================================="

# Run database migrations
echo "📦 Running database migrations..."
php artisan migrate --force

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

# Start the web server
# Using PHP built-in server directly for reliability
PORT="${PORT:-8080}"
echo "==========================================="
echo "🌍 Starting PHP server on 0.0.0.0:$PORT"
echo "==========================================="

# Use exec to replace the bash process with PHP
# This ensures proper signal handling and Railway can detect the process
exec php -S 0.0.0.0:$PORT -t public
