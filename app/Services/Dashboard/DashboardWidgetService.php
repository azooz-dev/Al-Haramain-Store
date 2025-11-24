<?php

namespace App\Services\Dashboard;

use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use App\Repositories\Interface\Analytics\OrderAnalyticsRepositoryInterface;
use App\Repositories\Interface\Analytics\UserAnalyticsRepositoryInterface;
use App\Repositories\Interface\Analytics\ProductAnalyticsRepositoryInterface;
use App\Repositories\Interface\Order\OrderRepositoryInterface;
use App\Models\Order\Order;

class DashboardWidgetService
{
    public function __construct(
        private OrderAnalyticsRepositoryInterface $orderAnalyticsRepository,
        private UserAnalyticsRepositoryInterface $userAnalyticsRepository,
        private ProductAnalyticsRepositoryInterface $productAnalyticsRepository,
        private OrderRepositoryInterface $orderRepository
    ) {}

    public function getTodaysRevenue(): float
    {
        $cacheKey = 'dashboard_widget_todays_revenue_' . today()->format('Y-m-d');
        
        return Cache::remember($cacheKey, now()->addMinutes(5), function () {
                return $this->orderAnalyticsRepository->getRevenueByDateRange(
                    today()->startOfDay(),
                    today()->endOfDay()
                );
            });
    }

    public function getRevenueGrowth(): string
    {
        $cacheKey = 'dashboard_widget_revenue_growth_' . today()->format('Y-m-d');
        
        return Cache::remember($cacheKey, now()->addMinutes(5), function () {
                $today = $this->getTodaysRevenue();
                $yesterday = $this->orderAnalyticsRepository->getRevenueByDateRange(
                    now()->subDay()->startOfDay(),
                    now()->subDay()->endOfDay()
                );

                if ($yesterday == 0) {
                    return $today > 0 ? '+100%' : '0%';
                }

                $growth = (($today - $yesterday) / $yesterday) * 100;
                return ($growth >= 0 ? '+' : '') . number_format($growth, 1) . '%' . __('app.widgets.kpi.from_yesterday');
            });
    }

    public function getTotalOrdersToday(): int
    {
        $cacheKey = 'dashboard_widget_total_orders_today_' . today()->format('Y-m-d');
        
        return Cache::remember($cacheKey, now()->addMinutes(5), function () {
                return $this->orderAnalyticsRepository->getOrdersCountByDateRange(
                    today()->startOfDay(),
                    today()->endOfDay()
                );
            });
    }

    public function getOrdersGrowth(): string
    {
        $cacheKey = 'dashboard_widget_orders_growth_' . today()->format('Y-m-d');
        
        return Cache::remember($cacheKey, now()->addMinutes(5), function () {
                $today = $this->getTotalOrdersToday();
                $yesterday = $this->orderAnalyticsRepository->getOrdersCountByDateRange(
                    now()->subDay()->startOfDay(),
                    now()->subDay()->endOfDay()
                );

                if ($yesterday == 0) {
                    return $today > 0 ? '+100%' : '0%';
                }

                $growth = (($today - $yesterday) / $yesterday) * 100;
                return ($growth >= 0 ? '+' : '') . number_format($growth, 1) . '%' . __('app.widgets.kpi.from_yesterday');
            });
    }

    public function getAverageOrderValue(Carbon $start, Carbon $end): float
    {
        $cacheKey = 'dashboard_widget_aov_' . $start->format('Y-m-d') . '_' . $end->format('Y-m-d');
        
        return Cache::remember($cacheKey, now()->addMinutes(5), function () use ($start, $end) {
                return $this->orderAnalyticsRepository->getAverageOrderValue($start, $end);
            });
    }

    public function getAOVGrowth(): string
    {
        $cacheKey = 'dashboard_widget_aov_growth_' . today()->format('Y-m-d');
        
        return Cache::remember($cacheKey, now()->addMinutes(5), function () {
                $currentAOV = $this->getAverageOrderValue(
                    now()->subDays(30)->startOfDay(),
                    now()->endOfDay()
                );

                $previousAOV = $this->orderAnalyticsRepository->getAverageOrderValue(
                    now()->subDays(60)->startOfDay(),
                    now()->subDays(30)->endOfDay()
                );

                if ($previousAOV == 0) {
                    return $currentAOV > 0 ? '+100%' : '0%';
                }

                $growth = (($currentAOV - $previousAOV) / $previousAOV) * 100;
                return ($growth >= 0 ? '+' : '') . number_format($growth, 1) . '%' . __('app.widgets.kpi.vs_last_month');
            });
    }

    public function getNewCustomersToday(): int
    {
        $cacheKey = 'dashboard_widget_new_customers_today_' . today()->format('Y-m-d');
        
        return Cache::remember($cacheKey, now()->addMinutes(5), function () {
                return $this->userAnalyticsRepository->getUsersCountByDateRange(
                    today()->startOfDay(),
                    today()->endOfDay()
                );
            });
    }

    public function getCustomerGrowth(): string
    {
        $cacheKey = 'dashboard_widget_customer_growth_' . today()->format('Y-m-d');
        
        return Cache::remember($cacheKey, now()->addMinutes(5), function () {
                $today = $this->getNewCustomersToday();
                $yesterday = $this->userAnalyticsRepository->getUsersCountByDateRange(
                    now()->subDay()->startOfDay(),
                    now()->subDay()->endOfDay()
                );

                if ($yesterday == 0) {
                    return $today > 0 ? '+100%' : '0%';
                }

                $growth = (($today - $yesterday) / $yesterday) * 100;
                return ($growth >= 0 ? '+' : '') . number_format($growth, 1) . '%' . __('app.widgets.kpi.from_yesterday');
            });
    }

    public function getPendingOrdersCount(): int
    {
        $cacheKey = 'dashboard_widget_pending_orders';
        
        return Cache::remember($cacheKey, now()->addMinutes(2), function () {
                return $this->orderAnalyticsRepository->getOrdersCountByStatus(Order::PENDING);
            });
    }

    public function getLowStockProductsCount(): int
    {
        $cacheKey = 'dashboard_widget_low_stock_products';
        
        return Cache::remember($cacheKey, now()->addMinutes(5), function () {
                return $this->productAnalyticsRepository->getLowStockProductsCount(10);
            });
    }

    public function getLast7DaysRevenue(): array
    {
        $cacheKey = 'dashboard_widget_last_7_days_revenue_' . today()->format('Y-m-d');
        
        return Cache::remember($cacheKey, now()->addMinutes(5), function () {
                $dates = collect(range(0, 6))
                    ->map(fn($i) => now()->subDays(6 - $i)->format('Y-m-d'));

                $revenueData = $this->orderAnalyticsRepository->getRevenueByDateRangeGrouped(
                    now()->subDays(6)->startOfDay(),
                    now()->endOfDay()
                )->pluck('revenue', 'date');

                return $dates->map(fn($date) => (float) ($revenueData->get($date, 0)))->toArray();
            });
    }

    public function getLast7DaysOrders(): array
    {
        $cacheKey = 'dashboard_widget_last_7_days_orders_' . today()->format('Y-m-d');
        
        return Cache::remember($cacheKey, now()->addMinutes(5), function () {
                $dates = collect(range(0, 6))
                    ->map(fn($i) => now()->subDays(6 - $i)->format('Y-m-d'));

                $ordersData = $this->orderAnalyticsRepository->getOrdersCountByDateRangeGrouped(
                    now()->subDays(6)->startOfDay(),
                    now()->endOfDay()
                )->pluck('count', 'date');

                return $dates->map(fn($date) => (int) ($ordersData->get($date, 0)))->toArray();
            });
    }

    public function getLast7DaysAOV(): array
    {
        $cacheKey = 'dashboard_widget_last_7_days_aov_' . today()->format('Y-m-d');
        
        return Cache::remember($cacheKey, now()->addMinutes(5), function () {
                $dates = collect(range(0, 6))
                    ->map(fn($i) => now()->subDays(6 - $i)->format('Y-m-d'));

                $aovData = $this->orderAnalyticsRepository->getAverageOrderValueGrouped(
                    now()->subDays(6)->startOfDay(),
                    now()->endOfDay()
                )->pluck('avg_value', 'date');

                return $dates->map(fn($date) => round((float) ($aovData->get($date, 0)), 2))->toArray();
            });
    }

    public function getLast7DaysCustomers(): array
    {
        $cacheKey = 'dashboard_widget_last_7_days_customers_' . today()->format('Y-m-d');
        
        return Cache::remember($cacheKey, now()->addMinutes(5), function () {
                $dates = collect();
                for ($i = 6; $i >= 0; $i--) {
                    $dates->push(now()->subDays($i)->format('Y-m-d'));
                }

                $customersData = $this->userAnalyticsRepository->getUsersCountByDateRangeGrouped(
                    now()->subDays(6)->startOfDay(),
                    now()->endOfDay()
                )->pluck('count', 'date');

                return $dates->map(fn($date) => (int) ($customersData->get($date, 0)))->toArray();
            });
    }
}

