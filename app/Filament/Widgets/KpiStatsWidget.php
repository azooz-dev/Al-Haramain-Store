<?php

namespace App\Filament\Widgets;

use App\Models\User\User;
use App\Models\Order\Order;
use App\Models\Product\Product;
use Illuminate\Support\Facades\DB;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class KpiStatsWidget extends BaseWidget
{
    protected static ?int $sort = 1;
    protected static bool $isLazy = false;


    protected function getStats(): array
    {
        $todayStart = now()->startOfDay();
        $todayEnd = now()->endOfDay();
        $yesterdayStart = now()->subDay()->endOfDay();
        $last30Days = now()->subDays(30);

        return [
            // Today's Revenue
            Stat::make(__('app.widgets.kpi.todays_revenue'), '$' . number_format($this->getTodaysRevenue(), 2))
                ->description($this->getRevenueGrowth())
                ->descriptionIcon($this->getRevenueGrowth() >= 0 ? "heroicon-m-arrow-trending-up" : "heroicon-m-arrow-trending-down")
                ->color($this->getRevenueGrowth() >= 0 ? 'success' : "danger")
                ->chart($this->getLast7DaysRevenue()),

            // Total Orders
            Stat::make(__('app.widgets.total_orders'), number_format($this->getTotalOrders()))
                ->description($this->getOrdersGrowth())
                ->descriptionIcon($this->getOrdersGrowth() >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($this->getOrdersGrowth() >= 0 ? 'success' : 'danger')
                ->chart($this->getLast7DaysOrders()),

            // Average Order Value
            Stat::make(__('app.widgets.average_order_value'), number_format($this->getAverageOrderValue(), 2))
                ->description($this->getAOVGrowth())
                ->descriptionIcon($this->getAOVGrowth() >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($this->getAOVGrowth() >= 0 ? 'success' : 'danger')
                ->chart($this->getLast7DaysAVO()),

            // New Customer
            Stat::make(__('app.widgets.new_customers'), number_format($this->getNewCustomersToday()))
                ->description($this->getCustomerGrowth())
                ->descriptionIcon($this->getCustomerGrowth() >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($this->getCustomerGrowth() >= 0 ? 'success' : 'danger')
                ->chart($this->getLast7DaysCustomers()),

            // Pending Orders
            Stat::make(__('app.widgets.pending_orders'), number_format($this->getPendingOrders()))
                ->description(__('app.widgets.kpi.requires.attention'))
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color($this->getPendingOrders() >= 0 ? 'success' : 'danger')
                ->url(route('filament.admin.resources.orders.index', ['tableFilters[status][values][0]' => Order::PENDING])),

            // New Customer
            Stat::make(__('app.widgets.low_stock_products'), number_format($this->getLowStockProducts()))
                ->description(__('app.widgets.kpi.needs.restocking'))
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color($this->getLowStockProducts() >= 0 ? 'success' : 'danger')
                ->url(route('filament.admin.resources.products.index', ['tableFilters[stock_status][stock]' => 'low_stock'])),
        ];
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
        return ($growth >= 0 ? '+' : '') . number_format($growth, 1) . '%' . __('app.widgets.kpi.from_yesterday');
    }

    private function getTotalOrders(): string
    {
        return Order::whereDate('created_at', today())->count();
    }

    private function getOrdersGrowth(): string
    {
        $today = $this->getTotalOrders();
        $yesterday = Order::whereDate('created_at',  now()->subDay()->toDateString())->count();

        if ($yesterday == 0) {
            return $today > 0 ? '+100%' : '0%';
        }

        $growth = (($today - $yesterday) / $yesterday) * 100;
        return ($growth >= 0 ? '+' : '') . number_format($growth, 1) . '%' . __('app.widgets.kpi.from_yesterday');
    }

    private function getAverageOrderValue(): float
    {
        $orders = Order::where('status', '!=', Order::CANCELLED)
            ->where('status', '!=', Order::REFUNDED)
            ->whereBetween('created_at', [now()->subDays(30), now()]);

        $totalRevenue = $orders->sum('total_amount');
        $totalOrders = $orders->count();

        return $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;
    }

    private function getAOVGrowth(): string
    {
        $currentAVO = $this->getAverageOrderValue();

        $perviousOrders = Order::where('status', '!=', Order::CANCELLED)
            ->where('status', '!=', Order::REFUNDED)
            ->whereBetween('created_at', [now()->subDays(60), now()->subDays(30)]);

        $perviousRevenue = $perviousOrders->sum('total_amount');
        $perviousOrderCount = $perviousOrders->count();
        $perviousAVO = $perviousOrderCount > 0 ? $perviousRevenue / $perviousOrderCount : 0;

        if ($perviousAVO == 0) {
            return $currentAVO > 0 ? '+100%' : '0%';
        }

        $growth = (($currentAVO - $perviousAVO) / $perviousAVO) * 100;
        return ($growth >= 0 ? '+' : '') . number_format($growth, 1) . '%' . __('app.widgets.kpi.vs_last_month');
    }

    private function getNewCustomersToday(): int
    {
        return User::whereDate('created_at', today())->count();
    }

    private function getCustomerGrowth(): string
    {
        $today = $this->getNewCustomersToday();
        $yesterday = User::whereDate('created_at',  now()->subDay()->toDateString())->count();

        if ($yesterday == 0) {
            return $today > 0 ? '+100%' : '0%';
        }

        $growth = (($today - $yesterday) / $yesterday) * 100;
        return ($growth >= 0 ? '+' : '') . number_format($growth, 1) . '%' . __('app.widgets.kpi.from_yesterday');
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


    private function getLast7DaysAVO(): array
    {
        $data = Order::where('status', '!=', Order::CANCELLED)
            ->where('status', '!=', Order::REFUNDED)
            ->whereBetween('created_at', [now()->subDays(6)->startOfDay(), now()->endOfDay()])
            ->selectRaw('DATE(created_at) as Date, AVG(total_amount) as avg_value')
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
}
