<?php

namespace App\Repositories\Interface\Coupon;

interface CouponRepositoryInterface
{
  public function applyCoupon(int $couponId);
}
