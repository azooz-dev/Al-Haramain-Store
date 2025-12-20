<?php

namespace Modules\Analytics\Contracts;

use Carbon\Carbon;

interface ReviewAnalyticsServiceInterface
{
    /**
     * Get average rating for a date range
     */
    public function getAverageRating(Carbon $start, Carbon $end): float;

    /**
     * Get total reviews count for a date range
     */
    public function getTotalReviews(Carbon $start, Carbon $end): int;

    /**
     * Get count of pending reviews
     */
    public function getPendingReviewsCount(): int;

    /**
     * Get rating distribution data for charts
     */
    public function getRatingDistribution(Carbon $start, Carbon $end): array;
}

