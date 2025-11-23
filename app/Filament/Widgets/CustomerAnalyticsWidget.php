<?php

namespace App\Filament\Widgets;

use App\Filament\Concerns\ResolvesServices;
use App\Services\Dashboard\CustomerAnalyticsService;
use Filament\Widgets\ChartWidget;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;

class CustomerAnalyticsWidget extends ChartWidget
{
    use HasWidgetShield, ResolvesServices;

    protected static ?string $heading = null;
    protected static ?int $sort = 5;
    protected static ?string $maxHeight = '350px';
    protected static bool $isLazy = true;
    protected int | string | array $columnSpan = [
        'md' => 2,
        'xl' => 1,
    ];

    public ?string $filter = 'last_30_days';

    protected function getFilters(): ?array
    {
        return [
            'last_7_days' => __('app.widgets.customers.filters.last_7_days'),
            'last_30_days' => __('app.widgets.customers.filters.last_30_days'),
            'last_90_days' => __('app.widgets.customers.filters.last_90_days'),
            'this_year' => __('app.widgets.customers.filters.this_year'),
        ];
    }

    public function getHeading(): ?string
    {
        return __('app.widgets.customers.customer_analytics');
    }

    public function getDescription(): ?string
    {
        $service = $this->resolveService(CustomerAnalyticsService::class);
        $period = $this->getPeriodDates();
        $newCustomers = $service->getNewCustomers(
            $period['start'],
            $period['end']
        );
        $returningCustomers = $service->getReturningCustomers(
            $period['start'],
            $period['end']
        );
        $retentionRate = $newCustomers > 0 ? round(($returningCustomers / ($newCustomers + $returningCustomers)) * 100, 1) : 0;

        return __('app.widgets.customers.description', [
            'new' => number_format($newCustomers),
            'returning' => number_format($returningCustomers),
            'retention' => $retentionRate . '%'
        ]);
    }

    protected function getData(): array
    {
        $service = $this->resolveService(CustomerAnalyticsService::class);
        $period = $this->getPeriodDates();
        return $service->getCustomerAcquisitionData(
            $period['start'],
            $period['end']
        );
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'responsive' => true,
            'maintainAspectRatio' => false,
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'title' => [
                        'display' => true,
                        'text' => __('app.widgets.customers.customers_count')
                    ],
                ],
                'x' => [
                    'title' => [
                        'display' => true,
                        'text' => __('app.widgets.customers.date')
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
