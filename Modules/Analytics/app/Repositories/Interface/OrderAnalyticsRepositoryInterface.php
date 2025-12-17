<?php

namespace Modules\Analytics\Repositories\Interface;

use Carbon\Carbon;
use Illuminate\Support\Collection;

interface OrderAnalyticsRepositoryInterface
{
    public function getRevenueByDateRange(Carbon $start, Carbon $end): float;

    public function getRevenueByDateRangeGrouped(Carbon $start, Carbon $end): Collection;

    public function getOrdersCountByDateRange(Carbon $start, Carbon $end): int;

    public function getOrdersCountByDateRangeGrouped(Carbon $start, Carbon $end): Collection;

    public function getOrdersCountByStatus(string $status, ?Carbon $start = null, ?Carbon $end = null): int;

    public function getOrdersCountByStatusGrouped(Carbon $start, Carbon $end): Collection;

    public function getAverageOrderValue(Carbon $start, Carbon $end): float;

    public function getAverageOrderValueGrouped(Carbon $start, Carbon $end): Collection;

    public function getRecentOrders(int $limit = 15): Collection;
}

