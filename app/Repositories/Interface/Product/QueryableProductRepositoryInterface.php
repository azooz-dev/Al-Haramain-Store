<?php

namespace App\Repositories\Interface\Product;

use Illuminate\Database\Eloquent\Builder;

interface QueryableProductRepositoryInterface
{
    public function getQueryBuilder(): Builder;
}


