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
     * Get orders for the authenticated user
     *
     * @param int|null $userId Optional user ID. If not provided, will try to get from request context
     * @return \Illuminate\Support\Collection
     */
    public function getUserOrders(?int $userId = null);

    /**
     * Find order by ID
     *
     * @param int $orderId
     * @return OrderApiResource
     */
    public function findOrderById(int $orderId);

    /**
     * Check if order is delivered
     *
     * @param int $orderId
     * @return bool
     */
    public function isDelivered(int $orderId): bool;
}

