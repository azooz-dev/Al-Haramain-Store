<?php

namespace Modules\Order\Contracts;

use Modules\Order\Http\Resources\Order\OrderApiResource;

interface OrderServiceInterface
{
    /**
     * Store order and return Order resource or errorResponse array
     *
     * @param array $data
     * @return OrderApiResource|array
     */
    public function storeOrder(array $data);

    /**
     * Check if order is delivered
     *
     * @param int $orderId
     * @return bool
     */
    public function isDelivered(int $orderId): bool;
}

