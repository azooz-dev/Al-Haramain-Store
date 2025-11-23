<?php

namespace App\Filament\Widgets;

use App\Filament\Concerns\ResolvesServices;
use App\Models\Order\Order;
use App\Services\Dashboard\DashboardWidgetService;
use Filament\Widgets\StatsOverviewWidget\Stat;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class KpiStatsWidget extends BaseWidget
{
    use HasWidgetShield, ResolvesServices;

    protected static ?int $sort = 1;
    protected static bool $isLazy = true;

    protected function getStats(): array
    {
        $service = $this->resolveService(DashboardWidgetService::class);

        $revenueGrowth = $service->getRevenueGrowth();
        $ordersGrowth = $service->getOrdersGrowth();
        $aovGrowth = $service->getAOVGrowth();
        $customerGrowth = $service->getCustomerGrowth();

        return [
            // Today's Revenue
            Stat::make(__('app.widgets.kpi.todays_revenue'), '$' . number_format($service->getTodaysRevenue(), 2))
                ->description($revenueGrowth)
                ->descriptionIcon(str_starts_with($revenueGrowth, '+') || $revenueGrowth === '0%' ? "heroicon-m-arrow-trending-up" : "heroicon-m-arrow-trending-down")
                ->color(str_starts_with($revenueGrowth, '+') || $revenueGrowth === '0%' ? 'success' : "danger")
                ->chart($service->getLast7DaysRevenue()),

            // Total Orders
            Stat::make(__('app.widgets.total_orders'), number_format($service->getTotalOrdersToday()))
                ->description($ordersGrowth)
                ->descriptionIcon(str_starts_with($ordersGrowth, '+') || $ordersGrowth === '0%' ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color(str_starts_with($ordersGrowth, '+') || $ordersGrowth === '0%' ? 'success' : 'danger')
                ->chart($service->getLast7DaysOrders()),

            // Average Order Value
            Stat::make(__('app.widgets.average_order_value'), number_format($service->getAverageOrderValue(now()->subDays(30)->startOfDay(), now()->endOfDay()), 2))
                ->description($aovGrowth)
                ->descriptionIcon(str_starts_with($aovGrowth, '+') || $aovGrowth === '0%' ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color(str_starts_with($aovGrowth, '+') || $aovGrowth === '0%' ? 'success' : 'danger')
                ->chart($service->getLast7DaysAOV()),

            // New Customer
            Stat::make(__('app.widgets.new_customers'), number_format($service->getNewCustomersToday()))
                ->description($customerGrowth)
                ->descriptionIcon(str_starts_with($customerGrowth, '+') || $customerGrowth === '0%' ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color(str_starts_with($customerGrowth, '+') || $customerGrowth === '0%' ? 'success' : 'danger')
                ->chart($service->getLast7DaysCustomers()),

            // Pending Orders
            Stat::make(__('app.widgets.pending_orders'), number_format($service->getPendingOrdersCount()))
                ->description(__('app.widgets.kpi.requires.attention'))
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color($service->getPendingOrdersCount() >= 0 ? 'success' : 'danger')
                ->url(route('filament.admin.resources.orders.index', ['tableFilters[status][values][0]' => Order::PENDING])),

            // Low Stock Products
            Stat::make(__('app.widgets.low_stock_products'), number_format($service->getLowStockProductsCount()))
                ->description(__('app.widgets.kpi.needs.restocking'))
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color($service->getLowStockProductsCount() >= 0 ? 'success' : 'danger')
                ->url(route('filament.admin.resources.products.index', ['tableFilters[stock_status][stock]' => 'low_stock'])),
        ];
    }
}
