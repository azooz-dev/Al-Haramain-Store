<?php

namespace Modules\Analytics\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Collection;
use Modules\Analytics\Contracts\OrderAnalyticsServiceInterface;
use Modules\Analytics\Repositories\Interface\OrderAnalyticsRepositoryInterface;
use Modules\Order\Enums\OrderStatus;

class OrderAnalyticsService implements OrderAnalyticsServiceInterface
{
    public function __construct(
        private OrderAnalyticsRepositoryInterface $orderAnalyticsRepository
    ) {}

    public function getRevenueOverview(Carbon $start, Carbon $end): array
    {
        $cacheKey = 'dashboard_widget_revenue_overview_' . $start->format('Y-m-d') . '_' . $end->format('Y-m-d');
        
        return Cache::remember($cacheKey, now()->addMinutes(10), function () use ($start, $end) {
                $revenueData = $this->orderAnalyticsRepository->getRevenueByDateRangeGrouped($start, $end);
                $ordersData = $this->orderAnalyticsRepository->getOrdersCountByDateRangeGrouped($start, $end);

                // Merge data by date
                $dates = collect();
                $revenueMap = $revenueData->pluck('revenue', 'date');
                $ordersMap = $ordersData->pluck('count', 'date');

                // Get all unique dates
                $allDates = $revenueMap->keys()->merge($ordersMap->keys())->unique()->sort();

                $revenueArray = [];
                $ordersArray = [];
                $labels = [];

                foreach ($allDates as $date) {
                    $labels[] = Carbon::parse($date)->format('M j');
                    $revenueArray[] = (float) ($revenueMap->get($date, 0));
                    $ordersArray[] = (int) ($ordersMap->get($date, 0));
                }

                return [
                    'datasets' => [
                        [
                            'label' => __('app.widgets.revenue.revenue_label'),
                            'data' => $revenueArray,
                            'borderColor' => 'rgb(59, 130, 246)',
                            'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                            'fill' => true,
                            'tension' => 0.4,
                            'yAxisID' => 'y'
                        ],
                        [
                            'label' => __('app.widgets.revenue.orders_label'),
                            'data' => $ordersArray,
                            'borderColor' => 'rgb(16, 185, 129)',
                            'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                            'fill' => true,
                            'tension' => 0.4,
                            'yAxisID' => 'y1'
                        ],
                    ],
                    'labels' => $labels,
                ];
            });
    }

    public function getRevenueGrowthPercentage(Carbon $start, Carbon $end): float
    {
        $cacheKey = 'dashboard_widget_revenue_growth_' . $start->format('Y-m-d') . '_' . $end->format('Y-m-d');
        
        return Cache::remember($cacheKey, now()->addMinutes(10), function () use ($start, $end) {
                $periodLength = $start->diffInDays($end) + 1;
                $currentRevenue = $this->orderAnalyticsRepository->getRevenueByDateRange($start, $end);

                $previousStart = $start->copy()->subDays($periodLength);
                $previousEnd = $start->copy()->subDays(1);
                $previousRevenue = $this->orderAnalyticsRepository->getRevenueByDateRange($previousStart, $previousEnd);

                if ($previousRevenue == 0) {
                    return $currentRevenue > 0 ? 100 : 0;
                }

                return (($currentRevenue - $previousRevenue) / $previousRevenue) * 100;
            });
    }

    public function getOrderStatusDistribution(Carbon $start, Carbon $end): array
    {
        $cacheKey = 'dashboard_widget_order_status_distribution_' . $start->format('Y-m-d') . '_' . $end->format('Y-m-d');
        
        return Cache::remember($cacheKey, now()->addMinutes(10), function () use ($start, $end) {
                $statusData = $this->orderAnalyticsRepository->getOrdersCountByStatusGrouped($start, $end)
                    ->pluck('count', 'status');

                $labels = [];
                $data = [];
                $backgroundColors = [];

                foreach (OrderStatus::cases() as $status) {
                    $count = $statusData->get($status->value, 0);
                    if ($count > 0) {
                        $labels[] = $status->label();
                        $data[] = $count;
                        $backgroundColors[] = $status->chartColor();
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
            });
    }

    public function getRecentOrders(int $limit = 15): Collection
    {
        $cacheKey = 'dashboard_widget_recent_orders_' . $limit;
        
        return Cache::remember($cacheKey, now()->addMinutes(2), function () use ($limit) {
                return $this->orderAnalyticsRepository->getRecentOrders($limit);
            });
    }

    public function getTotalOrdersCount(Carbon $start, Carbon $end): int
    {
        $cacheKey = 'dashboard_widget_total_orders_' . $start->format('Y-m-d') . '_' . $end->format('Y-m-d');
        
        return Cache::remember($cacheKey, now()->addMinutes(5), function () use ($start, $end) {
                return $this->orderAnalyticsRepository->getOrdersCountByDateRange($start, $end);
            });
    }

    public function getTotalRevenue(Carbon $start, Carbon $end): float
    {
        $cacheKey = 'dashboard_widget_total_revenue_' . $start->format('Y-m-d') . '_' . $end->format('Y-m-d');
        
        return Cache::remember($cacheKey, now()->addMinutes(5), function () use ($start, $end) {
                return $this->orderAnalyticsRepository->getRevenueByDateRange($start, $end);
            });
    }

    private function darkenColor(string $color): string
    {
        // Simple darkening by reducing opacity slightly
        if (preg_match('/rgba\((\d+),\s*(\d+),\s*(\d+),\s*([\d.]+)\)/', $color, $matches)) {
            $opacity = max(0.3, (float)$matches[4] - 0.2);
            return "rgba({$matches[1]}, {$matches[2]}, {$matches[3]}, {$opacity})";
        }
        return $color;
    }
}
