<?php

namespace Modules\Analytics\Contracts;

use Carbon\Carbon;

interface CustomerAnalyticsServiceInterface
{
    /**
     * Get new customers count for a date range
     */
    public function getNewCustomers(Carbon $start, Carbon $end): int;

    /**
     * Get returning customers count for a date range
     */
    public function getReturningCustomers(Carbon $start, Carbon $end): int;

    /**
     * Get customer acquisition data for charts
     */
    public function getCustomerAcquisitionData(Carbon $start, Carbon $end): array;

    /**
     * Get top category by revenue
     */
    public function getTopCategoryByRevenue(Carbon $start, Carbon $end): ?string;
}

