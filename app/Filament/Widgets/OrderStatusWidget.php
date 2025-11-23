<?php

namespace App\Filament\Widgets;

use App\Filament\Concerns\ResolvesServices;
use App\Services\Dashboard\OrderAnalyticsService;
use Filament\Widgets\ChartWidget;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;

class OrderStatusWidget extends ChartWidget
{
    use HasWidgetShield, ResolvesServices;

    protected static ?string $heading = null;
    protected static ?int $sort = 1;
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
            'today' => __('app.widgets.orders.filters.today'),
            'last_7_days' => __('app.widgets.orders.filters.last_7_days'),
            'last_30_days' => __('app.widgets.orders.filters.last_30_days'),
            'this_year' => __('app.widgets.orders.filters.this_year'),
        ];
    }

    public function getHeading(): ?string
    {
        return __('app.widgets.orders.status_distribution');
    }

    public function getDescription(): ?string
    {
        $service = $this->resolveService(OrderAnalyticsService::class);
        $period = $this->getPeriodDates();
        $totalOrders = $service->getTotalOrdersCount($period['start'], $period['end']);
        return __('app.widgets.orders.total_orders_description', ['count' => number_format($totalOrders)]);
    }

    protected function getData(): array
    {
        $service = $this->resolveService(OrderAnalyticsService::class);
        $period = $this->getPeriodDates();
        return $service->getOrderStatusDistribution($period['start'], $period['end']);
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
                        'padding' => 15,
                    ],
                ],
                'tooltip' => [
                    'callbacks' => [
                        'label' => 'function(context) {
                            let label = context.label || "";
                            let value = context.parsed;
                            let total = context.dataset.data.reduce((a, b) => a + b, 0);
                            let percentage = ((value / total) * 100).toFixed(1);
                            return label + ": " + value + " (" + percentage + "%)";
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
            'today' => [
                'start' => now()->startOfDay(),
                'end' => now()->endOfDay(),
            ],
            'last_7_days' => [
                'start' => now()->subDays(6)->startOfDay(),
                'end' => now()->endOfDay(),
            ],
            'last_30_days' => [
                'start' => now()->subDays(29)->startOfDay(),
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
