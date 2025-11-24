<?php

namespace App\Repositories\Interface\Analytics;

use Carbon\Carbon;
use Illuminate\Support\Collection;

interface ReviewAnalyticsRepositoryInterface
{
    public function getReviewsCountByDateRange(Carbon $start, Carbon $end): int;

    public function getAverageRating(Carbon $start, Carbon $end): float;

    public function getRatingDistribution(Carbon $start, Carbon $end): Collection;
}

