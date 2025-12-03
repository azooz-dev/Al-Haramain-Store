<?php

namespace App\Repositories\Interface\Analytics;

use Modules\Catalog\Entities\Category\Category;
use Carbon\Carbon;

interface CategoryAnalyticsRepositoryInterface
{
  public function getTopCategoryByRevenue(Carbon $start, Carbon $end): ?Category;
}
