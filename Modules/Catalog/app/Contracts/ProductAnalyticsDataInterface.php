<?php

namespace Modules\Catalog\Contracts;

use Carbon\Carbon;
use Illuminate\Support\Collection;

interface ProductAnalyticsDataInterface
{
    /**
     * Get count of low stock products
     */
    public function getLowStockProductsCount(int $threshold = 10): int;

    /**
     * Get top selling products for a date range
     */
    public function getTopSellingProducts(Carbon $start, Carbon $end, int $limit = 3): Collection;
}

