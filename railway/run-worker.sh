#!/bin/bash
# ============================================
# Al-Haramain Store - Railway Queue Worker
# ============================================

echo "👷 Starting Queue Worker..."
php artisan config:clear

php artisan queue:work --sleep=3 --tries=3 --max-time=3600 --verbose
