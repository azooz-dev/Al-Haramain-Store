<?php

namespace App\Repositories\Interface\Coupon;

interface CouponRepositoryInterface
{
  public function findCoupon(string $couponCode);
}
