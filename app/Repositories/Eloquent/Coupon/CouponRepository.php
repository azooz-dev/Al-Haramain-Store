<?php

namespace App\Repositories\Eloquent\Coupon;

use App\Models\Coupon\Coupon;
use App\Repositories\Interface\Coupon\CouponRepositoryInterface;

class CouponRepository implements CouponRepositoryInterface
{
  public function applyCoupon(int $couponId)
  {
    return Coupon::find($couponId);
  }
}
