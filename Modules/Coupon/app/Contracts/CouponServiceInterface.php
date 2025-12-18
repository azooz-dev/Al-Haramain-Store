<?php

namespace Modules\Coupon\Contracts;

use Modules\Coupon\Entities\Coupon\Coupon;

interface CouponServiceInterface
{
    /**
     * Apply coupon to order and return new total amount after discount.
     *
     * @param string $couponCode
     * @param float $totalAmount
     * @param int $userId
     * @return float New total amount after discount
     */
    public function applyCouponToOrder(string $couponCode, float $totalAmount, int $userId): float;

    /**
     * Validate and retrieve coupon by code.
     *
     * @param string $couponCode
     * @param int $userId
     * @return Coupon
     */
    public function applyCoupon(string $couponCode, int $userId);
}

