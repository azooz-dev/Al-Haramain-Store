<?php

namespace App\Services\Dashboard;

use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use App\Repositories\Interface\Analytics\ReviewAnalyticsRepositoryInterface;
use Modules\Review\Repositories\Interface\Review\ReviewRepositoryInterface;
use Modules\Review\Entities\Review\Review;

class ReviewAnalyticsService
{
    public function __construct(
        private ReviewAnalyticsRepositoryInterface $reviewAnalyticsRepository,
        private ReviewRepositoryInterface $reviewRepository
    ) {}

    public function getAverageRating(Carbon $start, Carbon $end): float
    {
        $cacheKey = 'dashboard_widget_avg_rating_' . $start->format('Y-m-d') . '_' . $end->format('Y-m-d');
        
        return Cache::remember($cacheKey, now()->addMinutes(5), function () use ($start, $end) {
                return $this->reviewAnalyticsRepository->getAverageRating($start, $end);
            });
    }

    public function getTotalReviews(Carbon $start, Carbon $end): int
    {
        $cacheKey = 'dashboard_widget_total_reviews_' . $start->format('Y-m-d') . '_' . $end->format('Y-m-d');
        
        return Cache::remember($cacheKey, now()->addMinutes(5), function () use ($start, $end) {
                return $this->reviewAnalyticsRepository->getReviewsCountByDateRange($start, $end);
            });
    }

    public function getPendingReviewsCount(): int
    {
        $cacheKey = 'dashboard_widget_pending_reviews';
        
        return Cache::remember($cacheKey, now()->addMinutes(2), function () {
                return $this->reviewRepository->countByStatus(Review::PENDING);
            });
    }

    public function getRatingDistribution(Carbon $start, Carbon $end): array
    {
        $cacheKey = 'dashboard_widget_rating_distribution_' . $start->format('Y-m-d') . '_' . $end->format('Y-m-d');
        
        return Cache::remember($cacheKey, now()->addMinutes(10), function () use ($start, $end) {
                $ratingData = $this->reviewAnalyticsRepository->getRatingDistribution($start, $end)
                    ->pluck('count', 'rating');

                $labels = [];
                $data = [];
                $colors = [];

                for ($rating = 1; $rating <= 5; $rating++) {
                    $count = $ratingData->get($rating, 0);
                    $labels[] = str_repeat('⭐', $rating) . ' (' . $rating . ' Star)';
                    $data[] = $count;
                    $colors[] = $this->getRatingColor($rating);
                }

                return [
                    'datasets' => [
                        [
                            'data' => $data,
                            'backgroundColor' => $colors,
                            'borderColor' => array_map(fn($color) => $this->darkenColor($color), $colors),
                            'borderWidth' => 2,
                            'hoverOffset' => 4,
                        ],
                    ],
                    'labels' => $labels,
                ];
            });
    }

    private function getRatingColor(int $rating): string
    {
        return match ($rating) {
            1 => 'rgba(239, 68, 68, 0.8)',
            2 => 'rgba(245, 158, 11, 0.8)',
            3 => 'rgba(251, 191, 36, 0.8)',
            4 => 'rgba(34, 197, 94, 0.8)',
            5 => 'rgba(16, 185, 129, 0.8)',
            default => 'rgba(156, 163, 175, 0.8)',
        };
    }

    private function darkenColor(string $color): string
    {
        if (preg_match('/rgba\((\d+),\s*(\d+),\s*(\d+),\s*([\d.]+)\)/', $color, $matches)) {
            $opacity = max(0.3, (float)$matches[4] - 0.2);
            return "rgba({$matches[1]}, {$matches[2]}, {$matches[3]}, {$opacity})";
        }
        return $color;
    }
}

