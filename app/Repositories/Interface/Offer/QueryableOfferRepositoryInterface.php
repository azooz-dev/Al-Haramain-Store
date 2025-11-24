<?php

namespace App\Repositories\Interface\Offer;

use Illuminate\Database\Eloquent\Builder;

interface QueryableOfferRepositoryInterface
{
    public function getQueryBuilder(): Builder;
}


