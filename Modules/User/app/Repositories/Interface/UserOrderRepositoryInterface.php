<?php

namespace Modules\User\Repositories\Interface;

interface UserOrderRepositoryInterface
{
  public function getAllUserOrders(int $userId);
}
