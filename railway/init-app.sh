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
php artisan migrate:fresh --force

# Run database seeders
echo "🌱 Seeding database..."
php artisan db:seed --force

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

# Start the web server
# PORT is set by Railway automatically
PORT="${PORT:-8080}"
echo "🔍 DEBUG: PORT environment variable = '${PORT}'"
echo "🔍 DEBUG: Will use port: ${PORT}"
echo "🌐 Starting PHP server on 0.0.0.0:${PORT}..."
echo "🔍 DEBUG: Document root = $(pwd)/public"
exec php -S 0.0.0.0:${PORT} -t public public/server.php
