<?php

namespace Modules\Analytics\Repositories\Eloquent;

use Modules\User\Entities\User;
use Modules\Order\Enums\OrderStatus;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Modules\Analytics\Repositories\Interface\UserAnalyticsRepositoryInterface;

class UserAnalyticsRepository implements UserAnalyticsRepositoryInterface
{
    public function getUsersCountByDateRange(Carbon $start, Carbon $end): int
    {
        return User::whereBetween('created_at', [$start, $end])->count();
    }

    public function getUsersCountByDateRangeGrouped(Carbon $start, Carbon $end): Collection
    {
        return User::whereBetween('created_at', [$start, $end])
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }

    public function getReturningCustomersCount(Carbon $start, Carbon $end): int
    {
        // Users who have placed more than one order
        // where at least one order is within the date range
        $excludedStatuses = array_map(fn($status) => $status->value, OrderStatus::excludedFromStats());
        
        $userIds = DB::table('users')
            ->join('orders', 'users.id', '=', 'orders.user_id')
            ->whereBetween('orders.created_at', [$start, $end])
            ->whereNotIn('orders.status', $excludedStatuses)
            ->groupBy('users.id')
            ->havingRaw('COUNT(DISTINCT orders.id) > 1')
            ->pluck('users.id');

        return $userIds->count();
    }

    public function getReturningCustomersByDateGrouped(Carbon $start, Carbon $end): Collection
    {
        $excludedStatuses = array_map(fn($status) => $status->value, OrderStatus::excludedFromStats());
        
        // Group returning customers by date of their second+ order within the period
        return DB::table('users')
            ->join('orders', 'users.id', '=', 'orders.user_id')
            ->whereBetween('orders.created_at', [$start, $end])
            ->whereNotIn('orders.status', $excludedStatuses)
            ->selectRaw('DATE(orders.created_at) as date, COUNT(DISTINCT users.id) as count')
            ->groupBy('users.id', 'date')
            ->havingRaw('COUNT(DISTINCT orders.id) > 1')
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }
}
