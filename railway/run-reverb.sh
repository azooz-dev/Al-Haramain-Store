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
# --port=${PORT:-8080}: Use Railway's dynamic port or default to 8080
php artisan reverb:start --host=0.0.0.0 --port=${PORT:-8080} --debug
