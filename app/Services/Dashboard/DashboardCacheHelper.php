<?php

namespace App\Services\Dashboard;

use Illuminate\Support\Facades\Cache;

class DashboardCacheHelper
{
    /**
     * Clear all dashboard widget caches.
     * Since we can't use tags, we'll clear all keys with the dashboard_widget_ prefix.
     */
    public static function flushAll(): void
    {
        // Get all cache keys (this works with database cache)
        $cacheKeys = self::getDashboardCacheKeys();
        
        foreach ($cacheKeys as $key) {
            Cache::forget($key);
        }
    }

    /**
     * Get all dashboard widget cache key patterns.
     * This list should be updated when new cache keys are added.
     */
    private static function getDashboardCacheKeys(): array
    {
        $today = today()->format('Y-m-d');
        $keys = [];

        // DashboardWidgetService keys
        $keys[] = "dashboard_widget_todays_revenue_{$today}";
        $keys[] = "dashboard_widget_revenue_growth_{$today}";
        $keys[] = "dashboard_widget_total_orders_today_{$today}";
        $keys[] = "dashboard_widget_orders_growth_{$today}";
        $keys[] = "dashboard_widget_aov_growth_{$today}";
        $keys[] = "dashboard_widget_new_customers_today_{$today}";
        $keys[] = "dashboard_widget_customer_growth_{$today}";
        $keys[] = "dashboard_widget_pending_orders";
        $keys[] = "dashboard_widget_low_stock_products";
        $keys[] = "dashboard_widget_last_7_days_revenue_{$today}";
        $keys[] = "dashboard_widget_last_7_days_orders_{$today}";
        $keys[] = "dashboard_widget_last_7_days_aov_{$today}";
        $keys[] = "dashboard_widget_last_7_days_customers_{$today}";

        // Add keys for last 7 days to cover date variations
        for ($i = 0; $i < 7; $i++) {
            $date = now()->subDays($i)->format('Y-m-d');
            $keys[] = "dashboard_widget_todays_revenue_{$date}";
            $keys[] = "dashboard_widget_revenue_growth_{$date}";
            $keys[] = "dashboard_widget_total_orders_today_{$date}";
            $keys[] = "dashboard_widget_orders_growth_{$date}";
            $keys[] = "dashboard_widget_aov_growth_{$date}";
            $keys[] = "dashboard_widget_new_customers_today_{$date}";
            $keys[] = "dashboard_widget_customer_growth_{$date}";
            $keys[] = "dashboard_widget_last_7_days_revenue_{$date}";
            $keys[] = "dashboard_widget_last_7_days_orders_{$date}";
            $keys[] = "dashboard_widget_last_7_days_aov_{$date}";
            $keys[] = "dashboard_widget_last_7_days_customers_{$date}";
        }

        // OrderAnalyticsService keys (add common date ranges)
        $keys[] = "dashboard_widget_recent_orders_15";
        $keys[] = "dashboard_widget_pending_reviews";
        $keys[] = "dashboard_widget_low_stock_products_10";

        return $keys;
    }
}

