<?php

namespace App\Repositories\interface\Coupon;

interface CouponRepositoryInterface
{
  public function applyCoupon(int $couponId);
}
