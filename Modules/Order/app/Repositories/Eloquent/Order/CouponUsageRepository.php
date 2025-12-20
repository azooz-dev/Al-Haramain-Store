<?php

namespace Modules\Order\Repositories\Eloquent\Order;

use Modules\Order\Entities\Order\Order;
use Modules\Order\Enums\OrderStatus;
use Modules\Coupon\Contracts\CouponUsageRepositoryInterface;

class CouponUsageRepository implements CouponUsageRepositoryInterface
{
    public function countCouponUsage(int $couponId): int
    {
        return Order::where('coupon_id', $couponId)
            ->whereNotIn('status', [OrderStatus::CANCELLED->value, OrderStatus::REFUNDED->value])
            ->count();
    }

    public function countUserCouponUsage(int $couponId, int $userId): int
    {
        return Order::where('coupon_id', $couponId)
            ->where('user_id', $userId)
            ->whereNotIn('status', [OrderStatus::CANCELLED->value, OrderStatus::REFUNDED->value])
            ->count();
    }
}
