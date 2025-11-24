<?php

namespace App\Observers\Product;

use App\Models\Product\Product;
use App\Services\Dashboard\DashboardCacheHelper;
use App\Services\Cache\CacheService;

class ProductObserver
{
    public function __construct(private CacheService $cacheService) {}

    /**
     * Handle the Product "updated" event.
     */
    public function updated(Product $product): void
    {
        // Invalidate cache when product quantity changes
        if ($product->isDirty('quantity')) {
            DashboardCacheHelper::flushAll();
            $this->cacheService->flush(['dashboard', 'products']);
        }
    }
}

