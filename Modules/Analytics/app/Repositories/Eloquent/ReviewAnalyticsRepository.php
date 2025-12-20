<?php

namespace Modules\Analytics\Repositories\Eloquent;

use Modules\Review\Entities\Review\Review;
use Modules\Review\Enums\ReviewStatus;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Modules\Analytics\Repositories\Interface\ReviewAnalyticsRepositoryInterface;

class ReviewAnalyticsRepository implements ReviewAnalyticsRepositoryInterface
{
    public function getReviewsCountByDateRange(Carbon $start, Carbon $end): int
    {
        return Review::whereBetween('created_at', [$start, $end])->count();
    }

    public function getAverageRating(Carbon $start, Carbon $end): float
    {
        return Review::whereBetween('created_at', [$start, $end])
            ->where('status', ReviewStatus::APPROVED)
            ->avg('rating') ?? 0;
    }

    public function getRatingDistribution(Carbon $start, Carbon $end): Collection
    {
        return Review::whereBetween('created_at', [$start, $end])
            ->select('rating', DB::raw('COUNT(*) as count'))
            ->groupBy('rating')
            ->orderBy('rating')
            ->get();
    }
}

