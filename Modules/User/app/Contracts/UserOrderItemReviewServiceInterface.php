<?php

namespace Modules\User\Contracts;

interface UserOrderItemReviewServiceInterface
{
    /**
     * Store reviews for an order item
     */
    public function storeAllOrderReviews(array $data, int $userId, int $orderId, int $itemId);
}

