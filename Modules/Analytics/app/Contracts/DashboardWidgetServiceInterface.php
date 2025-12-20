<?php

namespace Modules\Analytics\Contracts;

use Carbon\Carbon;

interface DashboardWidgetServiceInterface
{
    /**
     * Get today's total revenue
     */
    public function getTodaysRevenue(): float;

    /**
     * Get revenue growth percentage compared to yesterday
     */
    public function getRevenueGrowth(): string;

    /**
     * Get total orders count for today
     */
    public function getTotalOrdersToday(): int;

    /**
     * Get orders growth percentage compared to yesterday
     */
    public function getOrdersGrowth(): string;

    /**
     * Get average order value for a date range
     */
    public function getAverageOrderValue(Carbon $start, Carbon $end): float;

    /**
     * Get AOV growth percentage compared to previous period
     */
    public function getAOVGrowth(): string;

    /**
     * Get new customers count for today
     */
    public function getNewCustomersToday(): int;

    /**
     * Get customer growth percentage compared to yesterday
     */
    public function getCustomerGrowth(): string;

    /**
     * Get count of pending orders
     */
    public function getPendingOrdersCount(): int;

    /**
     * Get count of low stock products
     */
    public function getLowStockProductsCount(): int;

    /**
     * Get revenue data for the last 7 days
     */
    public function getLast7DaysRevenue(): array;

    /**
     * Get orders count for the last 7 days
     */
    public function getLast7DaysOrders(): array;

    /**
     * Get average order value for the last 7 days
     */
    public function getLast7DaysAOV(): array;

    /**
     * Get new customers count for the last 7 days
     */
    public function getLast7DaysCustomers(): array;
}

