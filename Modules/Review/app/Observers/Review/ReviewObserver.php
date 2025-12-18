<?php

namespace Modules\Review\Observers\Review;

use Modules\Review\Entities\Review\Review;
use Modules\Review\Events\ReviewCreated;
use Modules\Review\Events\ReviewUpdated;

class ReviewObserver
{
    /**
     * Handle the Review "created" event.
     */
    public function created(Review $review): void
    {
        // Dispatch ReviewCreated event
        // Analytics module will listen and invalidate cache
        ReviewCreated::dispatch($review);
    }

    /**
     * Handle the Review "updated" event.
     */
    public function updated(Review $review): void
    {
        // Dispatch ReviewUpdated event when review status or rating changes
        // Analytics module will listen and invalidate cache
        if ($review->isDirty(['status', 'rating'])) {
            ReviewUpdated::dispatch($review);
        }
    }
}

