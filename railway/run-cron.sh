#!/bin/bash
# ============================================
# Al-Haramain Store - Railway Scheduler
# ============================================

echo "⏰ Starting Laravel Scheduler..."
php artisan config:clear


while true
do
    echo "$(date): Running scheduled tasks..."
    php artisan schedule:run --verbose --no-interaction &
    sleep 60
done
