<?php

namespace App\Observers\Product;

use App\Models\Product\Product;
use App\Services\Dashboard\DashboardCacheHelper;

class ProductObserver
{
    /**
     * Handle the Product "updated" event.
     */
    public function updated(Product $product): void
    {
        // Invalidate cache when product quantity changes
        if ($product->isDirty('quantity')) {
            DashboardCacheHelper::flushAll();
        }
    }
}

