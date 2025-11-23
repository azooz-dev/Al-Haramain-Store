<?php

namespace App\Observers\Review;

use App\Models\Review\Review;
use App\Services\Dashboard\DashboardCacheHelper;

class ReviewObserver
{
    /**
     * Handle the Review "created" event.
     */
    public function created(Review $review): void
    {
        // Invalidate dashboard widget cache
        DashboardCacheHelper::flushAll();
    }

    /**
     * Handle the Review "updated" event.
     */
    public function updated(Review $review): void
    {
        // Invalidate cache when review status or rating changes
        if ($review->isDirty(['status', 'rating'])) {
            DashboardCacheHelper::flushAll();
        }
    }
}

