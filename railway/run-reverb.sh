#!/bin/bash
# ============================================
# Al-Haramain Store - Railway Reverb (WebSocket)
# Runs Laravel Reverb for real-time features
# ============================================

echo "==========================================="
echo "🔌 Starting Laravel Reverb WebSocket Server..."
php artisan config:clear

echo "==========================================="

# Run Laravel Reverb
# --host=0.0.0.0: Listen on all interfaces
# 1. Start Reverb on internal port 8001 in the background
echo "🔌 Starting Reverb on internal port 8001..."
php artisan reverb:start --host=0.0.0.0 --port=8001 &

# 2. Configure and Start Nginx
# Replace the placeholder port with the actual Railway PORT
sed -i "s/listen 8080/listen ${PORT:-8080}/g" railway/reverb-nginx.conf

echo "🌐 Starting Nginx proxy on port ${PORT:-8080}..."
# Use absolute path for config, assuming /app is working dir root
nginx -c /app/railway/reverb-nginx.conf
