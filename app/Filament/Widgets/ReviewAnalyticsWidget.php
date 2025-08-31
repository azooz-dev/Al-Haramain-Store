<?php

namespace App\Filament\Widgets;

use App\Models\Review\Review;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class ReviewAnalyticsWidget extends ChartWidget
{
    protected static ?string $heading = null;
    protected static ?int $sort = 2;
    protected static ?string $maxHeight = '300px';
    protected int | string | array $columnSpan = [
        'md' => 2,
        'xl' => 1,
    ];

    public ?string $filter = 'last_30_days';

    protected function getFilters(): ?array
    {
        return [
            'last_7_days' => __('app.widgets.reviews.filters.last_7_days'),
            'last_30_days' => __('app.widgets.reviews.filters.last_30_days'),
            'last_90_days' => __('app.widgets.reviews.filters.last_90_days'),
            'this_year' => __('app.widgets.reviews.filters.this_year'),
        ];
    }

    public function getHeading(): ?string
    {
        return __('app.widgets.reviews.rating_distribution');
    }

    public function getDescription(): ?string
    {
        $averageRating = $this->getAverageRating();
        $totalReviews = $this->getTotalReviews();
        $pendingReviews = $this->getPendingReviews();

        return __('app.widgets.reviews.description', [
            'average' => number_format($averageRating, 1),
            'total' => number_format($totalReviews),
            'pending' => number_format($pendingReviews)
        ]);
    }

    protected function getData(): array
    {
        $period = $this->getPeriodDates();

        $ratingData = Review::whereBetween('created_at', [$period['start'], $period['end']])
            ->select('rating', DB::raw('COUNT(*) as count'))
            ->groupBy('rating')
            ->orderBy('rating')
            ->get()
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
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getOptions(): array
    {
        return [
            'responsive' => true,
            'maintainAspectRatio' => false,
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'bottom',
                    'labels' => [
                        'usePointStyle' => true,
                        'padding' => 12,
                        'font' => [
                            'size' => 11,
                        ],
                    ],
                ],
                'tooltip' => [
                    'callbacks' => [
                        'label' => 'function(context) {
                            let label = context.label || "";
                            let value = context.parsed;
                            let total = context.dataset.data.reduce((a, b) => a + b, 0);
                            let percentage = total > 0 ? ((value / total) * 100).toFixed(1) : "0";
                            return label + ": " + value + " reviews (" + percentage + "%)";
                        }'
                    ]
                ],
            ],
            'cutout' => '60%',
        ];
    }

    private function getPeriodDates(): array
    {
        return match ($this->filter) {
            'last_7_days' => [
                'start' => now()->subDays(6)->startOfDay(),
                'end' => now()->endOfDay(),
            ],
            'last_30_days' => [
                'start' => now()->subDays(29)->startOfDay(),
                'end' => now()->endOfDay(),
            ],
            'last_90_days' => [
                'start' => now()->subDays(89)->startOfDay(),
                'end' => now()->endOfDay(),
            ],
            'this_year' => [
                'start' => now()->startOfYear(),
                'end' => now()->endOfYear(),
            ],
            default => [
                'start' => now()->subDays(29)->startOfDay(),
                'end' => now()->endOfDay(),
            ],
        };
    }

    private function getAverageRating(): float
    {
        $period = $this->getPeriodDates();

        return Review::whereBetween('created_at', [$period['start'], $period['end']])
            ->where('status', Review::APPROVED)
            ->avg('rating') ?? 0;
    }

    private function getTotalReviews(): int
    {
        $period = $this->getPeriodDates();

        return Review::whereBetween('created_at', [$period['start'], $period['end']])
            ->count();
    }

    private function getPendingReviews(): int
    {
        return Review::where('status', Review::PENDING)->count();
    }

    private function getRatingColor(int $rating): string
    {
        return match ($rating) {
            1 => 'rgba(239, 68, 68, 0.8)',    // red - very bad
            2 => 'rgba(245, 158, 11, 0.8)',   // orange - bad
            3 => 'rgba(251, 191, 36, 0.8)',   // yellow - okay
            4 => 'rgba(34, 197, 94, 0.8)',    // light green - good
            5 => 'rgba(16, 185, 129, 0.8)',   // green - excellent
            default => 'rgba(107, 114, 128, 0.8)', // gray
        };
    }

    private function darkenColor(string $color): string
    {
        return str_replace('0.8)', '1)', $color);
    }
}
