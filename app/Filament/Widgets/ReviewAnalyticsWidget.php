<?php

namespace App\Filament\Widgets;

use App\Filament\Concerns\ResolvesServices;
use Modules\Analytics\Contracts\ReviewAnalyticsServiceInterface;
use Filament\Widgets\ChartWidget;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;

class ReviewAnalyticsWidget extends ChartWidget
{
    use HasWidgetShield, ResolvesServices;

    protected static ?string $heading = null;
    protected static ?int $sort = 2;
    protected static ?string $maxHeight = '300px';
    protected static bool $isLazy = true;
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
        $service = $this->resolveService(ReviewAnalyticsServiceInterface::class);
        $period = $this->getPeriodDates();
        $averageRating = $service->getAverageRating(
            $period['start'],
            $period['end']
        );
        $totalReviews = $service->getTotalReviews(
            $period['start'],
            $period['end']
        );
        $pendingReviews = $service->getPendingReviewsCount();

        return __('app.widgets.reviews.description', [
            'average' => number_format($averageRating, 1),
            'total' => number_format($totalReviews),
            'pending' => number_format($pendingReviews)
        ]);
    }

    protected function getData(): array
    {
        $service = $this->resolveService(ReviewAnalyticsServiceInterface::class);
        $period = $this->getPeriodDates();
        return $service->getRatingDistribution(
            $period['start'],
            $period['end']
        );
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
}
