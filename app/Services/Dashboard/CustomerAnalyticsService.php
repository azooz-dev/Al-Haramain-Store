<?php

namespace App\Services\Dashboard;

use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use App\Services\Category\CategoryTranslationService;
use App\Repositories\Interface\Analytics\UserAnalyticsRepositoryInterface;
use App\Repositories\Interface\Analytics\CategoryAnalyticsRepositoryInterface;

class CustomerAnalyticsService
{
    public function __construct(
        private UserAnalyticsRepositoryInterface $userAnalyticsRepository,
        private CategoryAnalyticsRepositoryInterface $categoryAnalyticsRepository,
        private CategoryTranslationService $categoryTranslationService
    ) {}

    public function getNewCustomers(Carbon $start, Carbon $end): int
    {
        $cacheKey = 'dashboard_widget_new_customers_' . $start->format('Y-m-d') . '_' . $end->format('Y-m-d');

        return Cache::remember($cacheKey, now()->addMinutes(5), function () use ($start, $end) {
            return $this->userAnalyticsRepository->getUsersCountByDateRange($start, $end);
        });
    }

    public function getReturningCustomers(Carbon $start, Carbon $end): int
    {
        $cacheKey = 'dashboard_widget_returning_customers_' . $start->format('Y-m-d') . '_' . $end->format('Y-m-d');

        return Cache::remember($cacheKey, now()->addMinutes(5), function () use ($start, $end) {
            return $this->userAnalyticsRepository->getReturningCustomersCount($start, $end);
        });
    }

    public function getCustomerAcquisitionData(Carbon $start, Carbon $end): array
    {
        $cacheKey = 'dashboard_widget_customer_acquisition_' . $start->format('Y-m-d') . '_' . $end->format('Y-m-d');

        return Cache::remember($cacheKey, now()->addMinutes(10), function () use ($start, $end) {
            $newCustomersData = $this->userAnalyticsRepository->getUsersCountByDateRangeGrouped($start, $end)
                ->pluck('count', 'date');
            $returningCustomersData = $this->userAnalyticsRepository->getReturningCustomersByDateGrouped($start, $end)
                ->pluck('count', 'date');

            // Get all unique dates
            $allDates = $newCustomersData->keys()->merge($returningCustomersData->keys())->unique()->sort();

            $newCustomersArray = [];
            $returningCustomersArray = [];
            $labels = [];

            foreach ($allDates as $date) {
                $labels[] = Carbon::parse($date)->format('M j');
                $newCustomersArray[] = (int) ($newCustomersData->get($date, 0));
                $returningCustomersArray[] = (int) ($returningCustomersData->get($date, 0));
            }

            return [
                'datasets' => [
                    [
                        'label' => __('app.widgets.customers.new_customers'),
                        'data' => $newCustomersArray,
                        'borderColor' => 'rgb(59, 130, 246)',
                        'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                        'fill' => true,
                        'tension' => 0.4,
                    ],
                    [
                        'label' => __('app.widgets.customers.returning_customers'),
                        'data' => $returningCustomersArray,
                        'borderColor' => 'rgb(16, 185, 129)',
                        'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                        'fill' => true,
                        'tension' => 0.4,
                    ],
                ],
                'labels' => $labels,
            ];
        });
    }

    public function getTopCategoryByRevenue(Carbon $start, Carbon $end): ?string
    {
        $cacheKey = 'dashboard_widget_top_category_' . $start->format('Y-m-d') . '_' . $end->format('Y-m-d');

        return Cache::remember($cacheKey, now()->addMinutes(10), function () use ($start, $end) {
            $category = $this->categoryAnalyticsRepository->getTopCategoryByRevenue($start, $end);

            if (!$category) {
                return null;
            }

            return $this->categoryTranslationService->getTranslatedName($category);
        });
    }
}
