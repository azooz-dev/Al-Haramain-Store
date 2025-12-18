<?php

namespace Modules\Coupon\Contracts;

interface CouponUsageRepositoryInterface
{
    /**
     * Count total usage of a coupon (excluding cancelled and refunded orders).
     *
     * @param int $couponId
     * @return int
     */
    public function countCouponUsage(int $couponId): int;

    /**
     * Count usage of a coupon by a specific user (excluding cancelled and refunded orders).
     *
     * @param int $couponId
     * @param int $userId
     * @return int
     */
    public function countUserCouponUsage(int $couponId, int $userId): int;
}

