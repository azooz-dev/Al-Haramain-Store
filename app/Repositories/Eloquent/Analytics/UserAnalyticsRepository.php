<?php

namespace App\Repositories\Eloquent\Analytics;

use App\Models\User\User;
use App\Models\Order\Order;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use App\Repositories\Interface\Analytics\UserAnalyticsRepositoryInterface;

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
        // First, get the user IDs that match the criteria
        $userIds = DB::table('users')
            ->join('orders', 'users.id', '=', 'orders.user_id')
            ->whereBetween('orders.created_at', [$start, $end])
            ->where('orders.status', '!=', Order::CANCELLED)
            ->where('orders.status', '!=', Order::REFUNDED)
            ->groupBy('users.id')
            ->havingRaw('COUNT(DISTINCT orders.id) > 1')
            ->pluck('users.id');

        // Then count the distinct user IDs
        return $userIds->count();
    }

    public function getReturningCustomersByDateGrouped(Carbon $start, Carbon $end): Collection
    {
        // Group returning customers by date of their second+ order within the period
        return DB::table('users')
            ->join('orders', 'users.id', '=', 'orders.user_id')
            ->whereBetween('orders.created_at', [$start, $end])
            ->where('orders.status', '!=', Order::CANCELLED)
            ->where('orders.status', '!=', Order::REFUNDED)
            ->selectRaw('DATE(orders.created_at) as date, COUNT(DISTINCT users.id) as count')
            ->groupBy('users.id', 'date')
            ->havingRaw('COUNT(DISTINCT orders.id) > 1')
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }
}

