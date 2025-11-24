<?php

namespace App\Repositories\Interface\Analytics;

use Carbon\Carbon;
use Illuminate\Support\Collection;

interface UserAnalyticsRepositoryInterface
{
    public function getUsersCountByDateRange(Carbon $start, Carbon $end): int;

    public function getUsersCountByDateRangeGrouped(Carbon $start, Carbon $end): Collection;

    public function getReturningCustomersCount(Carbon $start, Carbon $end): int;

    public function getReturningCustomersByDateGrouped(Carbon $start, Carbon $end): Collection;
}

