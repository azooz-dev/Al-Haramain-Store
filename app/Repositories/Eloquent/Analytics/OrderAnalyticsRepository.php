<?php

namespace App\Repositories\Eloquent\Analytics;

use App\Models\Order\Order;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use App\Repositories\Interface\Analytics\OrderAnalyticsRepositoryInterface;

class OrderAnalyticsRepository implements OrderAnalyticsRepositoryInterface
{
    public function getRevenueByDateRange(Carbon $start, Carbon $end): float
    {
        return Order::where('status', '!=', Order::CANCELLED)
            ->where('status', '!=', Order::REFUNDED)
            ->whereBetween('created_at', [$start, $end])
            ->sum('total_amount');
    }

    public function getRevenueByDateRangeGrouped(Carbon $start, Carbon $end): Collection
    {
        return Order::where('status', '!=', Order::CANCELLED)
            ->where('status', '!=', Order::REFUNDED)
            ->whereBetween('created_at', [$start, $end])
            ->selectRaw('DATE(created_at) as date, SUM(total_amount) as revenue')
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }

    public function getOrdersCountByDateRange(Carbon $start, Carbon $end): int
    {
        return Order::whereBetween('created_at', [$start, $end])->count();
    }

    public function getOrdersCountByDateRangeGrouped(Carbon $start, Carbon $end): Collection
    {
        return Order::whereBetween('created_at', [$start, $end])
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }

    public function getOrdersCountByStatus(string $status, ?Carbon $start = null, ?Carbon $end = null): int
    {
        $query = Order::where('status', $status);

        if ($start && $end) {
            $query->whereBetween('created_at', [$start, $end]);
        }

        return $query->count();
    }

    public function getOrdersCountByStatusGrouped(Carbon $start, Carbon $end): Collection
    {
        return Order::whereBetween('created_at', [$start, $end])
            ->select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->get();
    }

    public function getAverageOrderValue(Carbon $start, Carbon $end): float
    {
        $orders = Order::where('status', '!=', Order::CANCELLED)
            ->where('status', '!=', Order::REFUNDED)
            ->whereBetween('created_at', [$start, $end]);

        $totalRevenue = $orders->sum('total_amount');
        $totalOrders = $orders->count();

        return $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;
    }

    public function getAverageOrderValueGrouped(Carbon $start, Carbon $end): Collection
    {
        return Order::where('status', '!=', Order::CANCELLED)
            ->where('status', '!=', Order::REFUNDED)
            ->whereBetween('created_at', [$start, $end])
            ->selectRaw('DATE(created_at) as date, AVG(total_amount) as avg_value')
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }

    public function getRecentOrders(int $limit = 15): Collection
    {
        return Order::with([
            'user',
            'items.orderable' => function ($morphTo) {
                $morphTo->morphWith([
                    \App\Models\Product\Product::class => ['translations'],
                    \App\Models\Offer\Offer::class => ['translations'],
                ]);
            },
            'items.variant',
            'items.color',
            'payments',
        ])
            ->latest()
            ->limit($limit)
            ->get();
    }
}

