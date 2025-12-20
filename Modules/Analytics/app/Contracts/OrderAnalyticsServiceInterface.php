<?php

namespace Modules\Analytics\Contracts;

use Carbon\Carbon;
use Illuminate\Support\Collection;

interface OrderAnalyticsServiceInterface
{
    /**
     * Get revenue overview data for charts
     */
    public function getRevenueOverview(Carbon $start, Carbon $end): array;

    /**
     * Get revenue growth percentage compared to previous period
     */
    public function getRevenueGrowthPercentage(Carbon $start, Carbon $end): float;

    /**
     * Get order status distribution data for charts
     */
    public function getOrderStatusDistribution(Carbon $start, Carbon $end): array;

    /**
     * Get recent orders
     */
    public function getRecentOrders(int $limit = 15): Collection;

    /**
     * Get total orders count for a date range
     */
    public function getTotalOrdersCount(Carbon $start, Carbon $end): int;

    /**
     * Get total revenue for a date range
     */
    public function getTotalRevenue(Carbon $start, Carbon $end): float;
}

