<?php

namespace Modules\Analytics\Listeners;

use Modules\Order\Events\OrderStatusChanged;
use Modules\Analytics\Services\DashboardCacheHelper;
use App\Services\Cache\CacheService;

class InvalidateCacheOnOrderChange
{
    public function __construct(private CacheService $cacheService) {}

    /**
     * Handle the event.
     */
    public function handle(OrderStatusChanged $event): void
    {
        // Invalidate dashboard widget cache
        DashboardCacheHelper::flushAll();
        
        // Invalidate analytics cache
        $this->cacheService->flush(['dashboard', 'revenue', 'orders']);
    }
}

