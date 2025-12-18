<?php

namespace Modules\Catalog\Observers\Product;

use Modules\Catalog\Entities\Product\Product;
use Modules\Analytics\Services\DashboardCacheHelper;
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


