<?php

namespace App\Repositories\Interface\Coupon;

interface CouponRepositoryInterface
{
  public function findCoupon(int $couponId);
}
