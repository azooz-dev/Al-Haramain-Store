<?php

namespace App\Filament\Widgets;

use App\Models\User\User;
use App\Models\Order\Order;
use App\Models\Product\Product;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class OrderStatusWidget extends ChartWidget
{
    protected static ?string $heading = null;
    protected static ?int $sort = 1;
    protected static ?string $maxHeight = '300px';
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
        $totalOrders = $this->getTotalOrdersCount();
        return __('app.widgets.orders.total_orders_description', ['count' => number_format($totalOrders)]);
    }

    protected function getData(): array
    {
        $period = $this->getPeriodDates();

        $statusData = Order::whereBetween('created_at', [$period['start'], $period['end']])
            ->select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status');

        $statuses = [
            Order::PENDING => __('app.status.pending'),
            Order::PROCESSING => __('app.status.processing'),
            Order::SHIPPED => __('app.status.shipped'),
            Order::DELIVERED => __('app.status.delivered'),
            Order::CANCELLED => __('app.status.cancelled'),
            Order::REFUNDED => __('app.status.refunded'),
        ];

        $labels = [];
        $data = [];
        $backgroundColors = [];

        foreach ($statuses as $status => $label) {
            $count = $statusData->get($status, 0);
            if ($count > 0) {
                $labels[] = $label;
                $data[] = $count;
                $backgroundColors[] = $this->getStatusColor($status);
            }
        }

        return [
            'datasets' => [
                [
                    'data' => $data,
                    'backgroundColor' => $backgroundColors,
                    'borderColor' => array_map(fn($color) => $this->darkenColor($color), $backgroundColors),
                    'borderWidth' => 2,
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

    private function getTodaysRevenue(): float
    {
        return Order::where('status', '!=', Order::CANCELLED)
            ->where('status', '!=', Order::REFUNDED)
            ->whereDate('created_at', today())
            ->sum('total_amount');
    }

    private function getRevenueGrowth(): string
    {
        $today = $this->getTodaysRevenue();
        $yesterday = Order::where('status', '!=', Order::CANCELLED)
            ->where('status', '!=', Order::REFUNDED)
            ->whereDate('created_at',  now()->subDay()->toDateString())
            ->sum('total_amount');

        if ($yesterday == 0) {
            return $today > 0 ? '+100%' : '0%';
        }

        $growth = (($today - $yesterday) / $yesterday) * 100;
        return ($growth >= 0 ? '+' : '') . number_format($growth, 1) . '%';
    }

    private function getTotalOrders(): int
    {
        $period = $this->getPeriodDates();
        return Order::whereBetween('created_at', [$period['start'], $period['end']])->count();
    }

    private function getOrdersGrowth(): string
    {
        $period = $this->getPeriodDates();
        $current = $this->getTotalOrders();

        $periodLength = $period['start']->diffInDays($period['end']) + 1;
        $previousStart = $period['start']->copy()->subDays($periodLength);
        $previousEnd = $period['start']->copy()->subDay();

        $previous = Order::whereBetween('created_at', [$previousStart, $previousEnd])->count();

        if ($previous == 0) {
            return $current > 0 ? '+100%' : '0%';
        }

        $growth = (($current - $previous) / $previous) * 100;
        return ($growth >= 0 ? '+' : '') . number_format($growth, 1) . '%';
    }

    private function getAverageOrderValue(): float
    {
        $period = $this->getPeriodDates();

        $orders = Order::where('status', '!=', Order::CANCELLED)
            ->where('status', '!=', Order::REFUNDED)
            ->whereBetween('created_at', [$period['start'], $period['end']]);

        $totalRevenue = $orders->sum('total_amount');
        $totalOrders = $orders->count();

        return $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;
    }

    private function getAOVGrowth(): string
    {
        $current = $this->getAverageOrderValue();

        $period = $this->getPeriodDates();
        $periodLength = $period['start']->diffInDays($period['end']) + 1;
        $previousStart = $period['start']->copy()->subDays($periodLength);
        $previousEnd = $period['start']->copy()->subDay();

        $previousOrders = Order::where('status', '!=', Order::CANCELLED)
            ->where('status', '!=', Order::REFUNDED)
            ->whereBetween('created_at', [$previousStart, $previousEnd]);

        $previousRevenue = $previousOrders->sum('total_amount');
        $previousOrderCount = $previousOrders->count();
        $previous = $previousOrderCount > 0 ? $previousRevenue / $previousOrderCount : 0;

        if ($previous == 0) {
            return $current > 0 ? '+100%' : '0%';
        }

        $growth = (($current - $previous) / $previous) * 100;
        return ($growth >= 0 ? '+' : '') . number_format($growth, 1) . '%';
    }

    private function getNewCustomersToday(): int
    {
        return User::whereDate('created_at', today())->count();
    }

    private function getCustomerGrowth(): string
    {
        $today = $this->getNewCustomersToday();
        $yesterday = User::whereDate('created_at',  now()->subDay()->toDateString()())->count();

        if ($yesterday == 0) {
            return $today > 0 ? '+100%' : '0%';
        }

        $growth = (($today - $yesterday) / $yesterday) * 100;
        return ($growth >= 0 ? '+' : '') . number_format($growth, 1) . '%';
    }

    private function getPendingOrders(): int
    {
        return Order::where('status', Order::PENDING)->count();
    }

    private function getLowStockProducts(): int
    {
        return Product::where('quantity', '<=', 10)
            ->where('quantity', '>', 0)
            ->count();
    }

    private function getTotalOrdersCount(): int
    {
        $period = $this->getPeriodDates();
        return Order::whereBetween('created_at', [$period['start'], $period['end']])->count();
    }

    private function getLast7DaysRevenue(): array
    {
        return Order::where('status', '!=', Order::CANCELLED)
            ->where('status', '!=', Order::REFUNDED)
            ->whereBetween('created_at', [now()->subDays(6)->startOfDay(), now()->endOfDay()])
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy(DB::raw('DATE(created_at)'))
            ->pluck(DB::raw('SUM(total_amount)'))
            ->toArray();
    }

    private function getLast7DaysOrders(): array
    {
        return Order::whereBetween('created_at', [now()->subDays(6)->startOfDay(), now()->endOfDay()])
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy(DB::raw('DATE(created_at)'))
            ->pluck(DB::raw('COUNT(*)'))
            ->toArray();
    }

    private function getLast7DaysAOV(): array
    {
        $data = Order::where('status', '!=', Order::CANCELLED)
            ->where('status', '!=', Order::REFUNDED)
            ->whereBetween('created_at', [now()->subDays(6)->startOfDay(), now()->endOfDay()])
            ->selectRaw('DATE(created_at) as date, AVG(total_amount) as avg_value')
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('avg_value')
            ->toArray();

        return array_map(fn($value) => round($value, 2), $data);
    }

    private function getLast7DaysCustomers(): array
    {
        return User::whereBetween('created_at', [now()->subDays(6)->startOfDay(), now()->endOfDay()])
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy(DB::raw('DATE(created_at)'))
            ->pluck(DB::raw('COUNT(*)'))
            ->toArray();
    }

    private function getStatusColor(string $status): string
    {
        return match ($status) {
            Order::PENDING => 'rgba(251, 191, 36, 0.8)', // warning
            Order::PROCESSING => 'rgba(59, 130, 246, 0.8)', // info
            Order::SHIPPED => 'rgba(139, 92, 246, 0.8)', // primary
            Order::DELIVERED => 'rgba(16, 185, 129, 0.8)', // success
            Order::CANCELLED => 'rgba(239, 68, 68, 0.8)', // danger
            Order::REFUNDED => 'rgba(107, 114, 128, 0.8)', // gray
            default => 'rgba(107, 114, 128, 0.8)', // gray
        };
    }

    private function darkenColor(string $color): string
    {
        // Convert rgba to darker version for borders
        return str_replace('0.8)', '1)', $color);
    }
}
