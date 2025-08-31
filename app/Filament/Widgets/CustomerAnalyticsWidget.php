<?php

namespace App\Filament\Widgets;

use Carbon\Carbon;
use App\Models\User\User;
use App\Models\Order\Order;
use App\Models\Category\Category;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;
use App\Services\Category\CategoryTranslationService;

class CustomerAnalyticsWidget extends ChartWidget
{
    protected static ?string $heading = null;
    protected static ?int $sort = 5;
    protected static ?string $maxHeight = '350px';
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
        $newCustomers = $this->getNewCustomers();
        $returningCustomers = $this->getReturningCustomers();
        $retentionRate = $newCustomers > 0 ? round(($returningCustomers / ($newCustomers + $returningCustomers)) * 100, 1) : 0;

        return __('app.widgets.customers.description', [
            'new' => number_format($newCustomers),
            'returning' => number_format($returningCustomers),
            'retention' => $retentionRate . '%'
        ]);
    }

    protected function getData(): array
    {
        $period = $this->getPeriodDates();

        // Get customer acquisition data
        $acquisitionData = User::whereBetween('created_at', [$period['start'], $period['end']])
            ->selectRaw('DATE(created_at) as date, COUNT(*) as new_customers')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Get returning customers data (customers who made another order)
        $returningData = DB::table('users')
            ->join('orders as first_order', 'users.id', '=', 'first_order.user_id')
            ->join('orders as second_order', function ($join) {
                $join->on('users.id', '=', 'second_order.user_id')
                    ->where('second_order.id', '!=', DB::raw('first_order.id'))
                    ->where('second_order.created_at', '>', DB::raw('first_order.created_at'));
            })
            ->whereBetween('second_order.created_at', [$period['start'], $period['end']])
            ->selectRaw('DATE(second_order.created_at) as date, COUNT(DISTINCT users.id) as returning_customers')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        // Merge data
        $chartData = $acquisitionData->map(function ($item) use ($returningData) {
            return [
                'date' => $item->date,
                'new_customers' => $item->new_customers,
                'returning_customers' => $returningData->get($item->date)?->returning_customers ?? 0,
            ];
        });

        return [
            'datasets' => [
                [
                    'label' => __('app.widgets.customers.new_customers'),
                    'data' => $chartData->pluck('new_customers')->toArray(),
                    'backgroundColor' => 'rgba(59, 130, 246, 0.8)',
                    'borderColor' => 'rgb(59, 130, 246)',
                    'borderWidth' => 2,
                ],
                [
                    'label' => __('app.widgets.customers.returning_customers'),
                    'data' => $chartData->pluck('returning_customers')->toArray(),
                    'backgroundColor' => 'rgba(16, 185, 129, 0.8)',
                    'borderColor' => 'rgb(16, 185, 129)',
                    'borderWidth' => 2,
                ],
            ],
            'labels' => $chartData->pluck('date')->map(fn($date) => Carbon::parse($date)->format('M j'))->toArray(),
        ];
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

    private function getNewCustomers(): int
    {
        $period = $this->getPeriodDates();
        return User::whereBetween('created_at', [$period['start'], $period['end']])->count();
    }

    private function getReturningCustomers(): int
    {
        $period = $this->getPeriodDates();

        return DB::table('users')
            ->join('orders as first_order', 'users.id', '=', 'first_order.user_id')
            ->join('orders as second_order', function ($join) {
                $join->on('users.id', '=', 'second_order.user_id')
                    ->where('second_order.id', '!=', DB::raw('first_order.id'))
                    ->where('second_order.created_at', '>', DB::raw('first_order.created_at'));
            })
            ->whereBetween('second_order.created_at', [$period['start'], $period['end']])
            ->distinct('users.id')
            ->count('users.id');
    }

    private function getTopCategory(): ?string
    {
        $period = $this->getPeriodDates();
        $translationService = app(CategoryTranslationService::class);

        $topCategoryId = DB::table('categories')
            ->join('category_product', 'categories.id', '=', 'category_product.category_id')
            ->join('order_items', 'category_product.product_id', '=', 'order_items.product_id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.status', '!=', Order::CANCELLED)
            ->where('orders.status', '!=', Order::REFUNDED)
            ->whereBetween('orders.created_at', [$period['start'], $period['end']])
            ->select('categories.id', DB::raw('SUM(order_items.quantity * order_items.total_price) as revenue'))
            ->groupBy('categories.id')
            ->orderByDesc('revenue')
            ->value('categories.id');

        if (!$topCategoryId) {
            return null;
        }

        $category = Category::with('translations')->find($topCategoryId);
        return $category ? $translationService->getTranslatedName($category) : null;
    }
}
