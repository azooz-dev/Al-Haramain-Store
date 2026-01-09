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
echo "🌍 Starting web server on port ${PORT:-8080}..."
php artisan serve --host=0.0.0.0 --port=${PORT:-8080}
