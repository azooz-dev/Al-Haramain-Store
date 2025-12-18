<?php

namespace Modules\Analytics\Listeners;

use Modules\Analytics\Services\DashboardCacheHelper;
use App\Services\Cache\CacheService;

class InvalidateDashboardCache
{
    public function __construct(private CacheService $cacheService) {}

    /**
     * Handle the event.
     */
    public function handle($event): void
    {
        // Invalidate dashboard widget cache
        DashboardCacheHelper::flushAll();
        
        // Determine cache tags based on event type
        $tags = $this->getCacheTags($event);
        
        // Invalidate analytics cache
        if (!empty($tags)) {
            $this->cacheService->flush($tags);
        }
    }

    /**
     * Get cache tags based on event type.
     */
    private function getCacheTags($event): array
    {
        $eventClass = get_class($event);
        
        return match (true) {
            str_contains($eventClass, 'Order') => ['dashboard', 'revenue', 'orders'],
            str_contains($eventClass, 'User') => ['dashboard', 'customers'],
            str_contains($eventClass, 'Product') => ['dashboard', 'products'],
            str_contains($eventClass, 'Review') => ['dashboard', 'reviews'],
            default => ['dashboard'],
        };
    }
}

