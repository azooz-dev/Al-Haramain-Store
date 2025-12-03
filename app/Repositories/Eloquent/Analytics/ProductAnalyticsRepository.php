<?php

namespace App\Repositories\Eloquent\Analytics;

use Modules\Catalog\Entities\Product\Product;
use App\Models\Order\Order;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use App\Repositories\Interface\Analytics\ProductAnalyticsRepositoryInterface;

class ProductAnalyticsRepository implements ProductAnalyticsRepositoryInterface
{
    public function getLowStockProductsCount(int $threshold = 10): int
    {
        return Product::where('quantity', '<=', $threshold)->count();
    }

    public function getTopSellingProducts(Carbon $start, Carbon $end, int $limit = 3): Collection
    {
        return Product::query()
            ->select([
                'products.*',
                DB::raw('SUM(order_items.quantity) as total_sold'),
                DB::raw('SUM(order_items.quantity * order_items.total_price) as total_revenue'),
                DB::raw('COUNT(DISTINCT orders.id) as order_count')
            ])
            ->join('order_items', function ($join) {
                $join->on('products.id', '=', 'order_items.orderable_id')
                    ->where('order_items.orderable_type', '=', Product::class);
            })
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.status', '!=', Order::CANCELLED)
            ->where('orders.status', '!=', Order::REFUNDED)
            ->whereBetween('orders.created_at', [$start, $end])
            ->groupBy('products.id')
            ->orderByDesc('total_sold')
            ->limit($limit)
            ->with(['translations', 'colors', 'variants'])
            ->get();
    }
}

