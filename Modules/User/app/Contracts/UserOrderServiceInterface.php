<?php

namespace Modules\User\Contracts;

interface UserOrderServiceInterface
{
    /**
     * Get all orders for a user
     */
    public function getAllUserOrders(int $userId);
}

