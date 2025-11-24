<?php

namespace App\Repositories\Interface\Category;

use Illuminate\Database\Eloquent\Builder;

interface QueryableCategoryRepositoryInterface
{
    public function getQueryBuilder(): Builder;
}


