<?php

namespace App\Observers\Review;

use App\Models\Review\Review;
use App\Services\Dashboard\DashboardCacheHelper;
use App\Services\Cache\CacheService;

class ReviewObserver
{
    public function __construct(private CacheService $cacheService) {}

    /**
     * Handle the Review "created" event.
     */
    public function created(Review $review): void
    {
        // Invalidate dashboard widget cache
        DashboardCacheHelper::flushAll();
        $this->cacheService->flush(['dashboard', 'reviews']);
    }

    /**
     * Handle the Review "updated" event.
     */
    public function updated(Review $review): void
    {
        // Invalidate cache when review status or rating changes
        if ($review->isDirty(['status', 'rating'])) {
            DashboardCacheHelper::flushAll();
            $this->cacheService->flush(['dashboard', 'reviews']);
        }
    }
}

