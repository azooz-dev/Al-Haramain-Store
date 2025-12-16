<?php

namespace Modules\Order\Repositories\Interface\Order;

use Illuminate\Database\Eloquent\Builder;

interface QueryableOrderRepositoryInterface
{
    public function getQueryBuilder(): Builder;
}


