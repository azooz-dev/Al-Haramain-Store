<?php

namespace Modules\Analytics\Repositories\Eloquent;

use Modules\Catalog\Entities\Category\Category;
use Modules\Order\Entities\Order\Order;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Modules\Analytics\Repositories\Interface\CategoryAnalyticsRepositoryInterface;

class CategoryAnalyticsRepository implements CategoryAnalyticsRepositoryInterface
{
    public function getTopCategoryByRevenue(Carbon $start, Carbon $end): ?Category
    {
        $topCategoryId = DB::table('categories')
            ->join('category_product', 'categories.id', '=', 'category_product.category_id')
            ->join('order_items', function ($join) {
                $join->on('category_product.product_id', '=', 'order_items.orderable_id')
                    ->where('order_items.orderable_type', '=', \Modules\Catalog\Entities\Product\Product::class);
            })
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.status', '!=', Order::CANCELLED)
            ->where('orders.status', '!=', Order::REFUNDED)
            ->whereBetween('orders.created_at', [$start, $end])
            ->select('categories.id', DB::raw('SUM(order_items.quantity * order_items.total_price) as revenue'))
            ->groupBy('categories.id')
            ->orderByDesc('revenue')
            ->value('categories.id');

        if (!$topCategoryId) {
            return null;
        }

        return Category::with('translations')->find($topCategoryId);
    }
}
