<?php

namespace App\Filament\Widgets;

use App\Filament\Concerns\ResolvesServices;
use App\Services\Dashboard\OrderAnalyticsService;
use Filament\Widgets\ChartWidget;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;

class RevenueOverviewWidget extends ChartWidget
{
    use HasWidgetShield, ResolvesServices;

    protected static ?string $heading = 'Chart';
    protected static ?int $sort = 6;
    protected static ?string $maxHeight = '350px';
    protected static bool $isLazy = true;
    protected int | string | array $columnSpan = 'full';

    public ?string $filter = 'last_30_days';

    protected function getFilters(): ?array
    {
        return [
            'today' => __('app.widgets.revenue.filters.today'),
            'last_7_days' => __('app.widgets.revenue.filters.last_7_days'),
            'last_30_days' => __('app.widgets.revenue.filters.last_30_days'),
            'last_90_days' => __('app.widgets.revenue.filters.last_90_days'),
            'this_year' => __('app.widgets.revenue.filters.this_year'),
        ];
    }

    public function getHeading(): ?string
    {
        return __('app.widgets.revenue.heading');
    }

    private function getPeriodDates(): array
    {
        return match ($this->filter) {
            'today' => [
                'start' => now()->startOfDay(),
                'end' => now()->endOfDay()
            ],
            'last_7_days' => [
                'start' => now()->subDays(6)->startOfDay(),
                'end' => now()->endOfDay()
            ],
            'last_30_days' => [
                'start' => now()->subDays(29)->startOfDay(),
                'end' => now()->endOfDay()
            ],
            'last_90_days' => [
                'start' => now()->subDays(89)->startOfDay(),
                'end' => now()->endOfDay()
            ],
            'this_year' => [
                'start' => now()->startOfYear(),
                'end' => now()->endOfDay()
            ],
            default => [
                'start' => now()->subDays(29)->startOfDay(),
                'end' => now()->endOfDay()
            ]
        };
    }


    protected function getOptions(): array
    {
        return [
            'responsive' => true,
            'maintainAspectRatio' => false,
            'interaction' => [
                'mode' => 'index',
                'intersect' => false,
            ],
            'scales' => [
                'y' => [
                    'type' => 'linear',
                    'display' => true,
                    'position' => 'left',
                    'title' => [
                        'display' => true,
                        'text' => __('app.widgets.revenue.revenue_axis'),
                    ],
                    'beginAtZero' => true,
                ],
                'y1' => [
                    'type' => 'linear',
                    'display' => true,
                    'position' => 'right',
                    'title' => [
                        'display' => true,
                        'text' => __('app.widgets.revenue.orders_axis'),
                    ],
                    'beginAtZero' => true,
                    'grid' => [
                        'drawOnChartArea' => false,
                    ],
                ],
            ],
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'top',
                ],
                'tooltip' => [
                    'mode' => 'index',
                    'intersect' => false,
                ],
            ],
        ];
    }

    public function getDescription(): string
    {
        $service = $this->resolveService(OrderAnalyticsService::class);
        $period = $this->getPeriodDates();
        $totalRevenue = $service->getTotalRevenue($period['start'], $period['end']);
        $growth = $service->getRevenueGrowthPercentage($period['start'], $period['end']);

        return __('app.widgets.revenue.description', [
            'total' => '$' . number_format($totalRevenue, 2),
            'growth' => $growth > 0 ? '+' . number_format($growth, 1) . '%' : number_format($growth, 1) . "%"
        ]);
    }

    protected function getData(): array
    {
        $service = $this->resolveService(OrderAnalyticsService::class);
        $period = $this->getPeriodDates();
        return $service->getRevenueOverview($period['start'], $period['end']);
    }

    protected function getType(): string
    {
        return 'line';
    }
}
