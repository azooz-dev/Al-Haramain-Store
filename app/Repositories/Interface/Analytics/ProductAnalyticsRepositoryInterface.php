<?php

namespace App\Repositories\Interface\Analytics;

use Carbon\Carbon;
use Illuminate\Support\Collection;

interface ProductAnalyticsRepositoryInterface
{
    public function getLowStockProductsCount(int $threshold = 10): int;

    public function getTopSellingProducts(Carbon $start, Carbon $end, int $limit = 3): Collection;
}

