<?php

namespace App\Repositories\Interface\Order;

use App\Models\Order\Order;

interface OrderRepositoryInterface
{
  public function store(array $data): Order;
}
