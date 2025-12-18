<?php

namespace Modules\Review\Events;

use Modules\Review\Entities\Review\Review;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ReviewUpdated
{
    use Dispatchable, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public Review $review
    ) {}
}

