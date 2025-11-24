<?php

namespace App\Services\Dashboard;

use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use App\Models\Product\Product;
use App\Repositories\Interface\Analytics\ProductAnalyticsRepositoryInterface;
use App\Services\Product\ProductTranslationService;

class ProductAnalyticsService
{
    public function __construct(
        private ProductAnalyticsRepositoryInterface $productAnalyticsRepository,
        private ProductTranslationService $productTranslationService
    ) {}

    public function getTopSellingProducts(Carbon $start, Carbon $end, int $limit = 3): \Illuminate\Support\Collection
    {
        $cacheKey = 'dashboard_widget_top_products_' . $start->format('Y-m-d') . '_' . $end->format('Y-m-d') . '_' . $limit;

        return Cache::remember($cacheKey, now()->addMinutes(10), function () use ($start, $end, $limit) {
                return $this->productAnalyticsRepository->getTopSellingProducts($start, $end, $limit);
            });
    }

    public function getLowStockProductsCount(int $threshold = 10): int
    {
        $cacheKey = 'dashboard_widget_low_stock_products_' . $threshold;

        return Cache::remember($cacheKey, now()->addMinutes(5), function () use ($threshold) {
                return $this->productAnalyticsRepository->getLowStockProductsCount($threshold);
            });
    }

    public function getTranslatedProductName(Product $product): string
    {
        return $this->productTranslationService->getTranslatedName($product);
    }
}
