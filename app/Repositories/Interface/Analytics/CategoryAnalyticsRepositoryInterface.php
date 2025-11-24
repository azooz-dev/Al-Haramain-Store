<?php

namespace App\Repositories\Interface\Analytics;

use App\Models\Category\Category;
use Carbon\Carbon;

interface CategoryAnalyticsRepositoryInterface
{
  public function getTopCategoryByRevenue(Carbon $start, Carbon $end): ?Category;
}
