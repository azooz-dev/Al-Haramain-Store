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
# --port=8080: Default port for Railway (Railway will map this)
php artisan reverb:start --host=0.0.0.0 --port=8080 --debug
