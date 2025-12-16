<?php

namespace Modules\Review\Repositories\Interface\Review;

use Illuminate\Database\Eloquent\Builder;

interface QueryableReviewRepositoryInterface
{
    public function getQueryBuilder(): Builder;
}


