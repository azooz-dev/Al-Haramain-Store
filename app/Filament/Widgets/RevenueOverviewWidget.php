<?php

namespace App\Filament\Widgets;

use Carbon\Carbon;
use App\Models\Order\Order;
use Filament\Widgets\ChartWidget;

class RevenueOverviewWidget extends ChartWidget
{
    protected static ?string $heading = 'Chart';
    protected static ?int $sort = 6;
    protected static ?string $maxHeight = '350px';
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

    private function getTotalRevenue(): float
    {
        $period = $this->getPeriodDates();
        return Order::where('status', '!=', Order::CANCELLED)
            ->where('status', '!=', Order::REFUNDED)
            ->whereBetween('created_at', [$period['start'], $period['end']])
            ->sum('total_amount');
    }

    private function getGrowthPercentage(): float
    {
        $period = $this->getPeriodDates();
        $periodLength = $period['start']->diffInDays($period['end']) + 1;

        $currentRevenue = $this->getTotalRevenue();

        $perviousStart = $period['start']->copy()->subDays($periodLength);
        $perviousEnd = $period['start']->copy()->subDays();

        $perviousRevenue = Order::where('status', '!=', Order::CANCELLED)
            ->where('status', '!=', Order::REFUNDED)
            ->whereBetween('created_at', [$perviousStart, $perviousEnd])
            ->sum('total_amount');

        if ($perviousRevenue == 0) {
            return $currentRevenue > 0 ? 100 : 0;
        }
        return (($currentRevenue - $perviousRevenue) / $perviousRevenue) * 100;
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
        $totalRevenue = $this->getTotalRevenue();
        $growth = $this->getGrowthPercentage();

        return __('app.widgets.revenue.description', [
            'total' => '$' . number_format($totalRevenue, 2),
            'growth' => $growth > 0 ? '+' . number_format($growth, 1) . '%' : number_format($growth, 1) . "%"
        ]);
    }

    protected function getData(): array
    {
        $period = $this->getPeriodDates();

        $data = Order::where('status', '!=', Order::CANCELLED)
            ->where('status', '!=', Order::REFUNDED)
            ->whereBetween('created_at', [$period['start'], $period['end']])
            ->selectRaw('DATE(created_at) as date, SUM(total_amount) as revenue, COUNT(*) as orders')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return [
            'datasets' => [
                [
                    'label' => __('app.widgets.revenue.revenue_label'),
                    'data' => $data->pluck('revenue')->toArray(),
                    'borderColor' => 'rgb(59, 130, 246)',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'fill' => true,
                    'tension' => 0.4,
                    'yAxisID' => 'y'
                ],
                [
                    'label' => __('app.widgets.revenue.orders_label'),
                    'data' => $data->pluck('orders')->toArray(),
                    'borderColor' => 'rgb(16, 185, 129)',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                    'fill' => true,
                    'tension' => 0.4,
                    'yAxisID' => 'y1'
                ],
            ],
            'labels' => $data->pluck('date')->map(fn($date) => Carbon::parse($date)->format('M j'))->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
