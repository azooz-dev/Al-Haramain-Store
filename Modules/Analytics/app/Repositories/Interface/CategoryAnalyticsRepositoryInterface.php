<?php

namespace Modules\Analytics\Repositories\Interface;

use Modules\Catalog\Entities\Category\Category;
use Carbon\Carbon;

interface CategoryAnalyticsRepositoryInterface
{
  public function getTopCategoryByRevenue(Carbon $start, Carbon $end): ?Category;
}
