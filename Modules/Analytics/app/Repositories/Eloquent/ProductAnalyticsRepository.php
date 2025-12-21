<?php

namespace Modules\Analytics\Repositories\Eloquent;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Modules\Analytics\Repositories\Interface\ProductAnalyticsRepositoryInterface;
use Modules\Catalog\Contracts\ProductAnalyticsDataInterface;

class ProductAnalyticsRepository implements ProductAnalyticsRepositoryInterface
{
    public function __construct(
        private ProductAnalyticsDataInterface $productAnalyticsData
    ) {}

    public function getLowStockProductsCount(int $threshold = 10): int
    {
        return $this->productAnalyticsData->getLowStockProductsCount($threshold);
    }

    public function getTopSellingProducts(Carbon $start, Carbon $end, int $limit = 3): Collection
    {
        return $this->productAnalyticsData->getTopSellingProducts($start, $end, $limit);
    }
}
