<?php

namespace Modules\Analytics\Contracts;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Modules\Catalog\Entities\Product\Product;

interface ProductAnalyticsServiceInterface
{
    /**
     * Get top selling products for a date range
     */
    public function getTopSellingProducts(Carbon $start, Carbon $end, int $limit = 3): Collection;

    /**
     * Get count of low stock products
     */
    public function getLowStockProductsCount(int $threshold = 10): int;

    /**
     * Get translated product name
     */
    public function getTranslatedProductName(Product $product): string;
}

