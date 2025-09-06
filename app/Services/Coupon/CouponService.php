<?php

namespace App\Services\Coupon;

use App\Exceptions\Coupon\CouponException;
use App\Models\Coupon\Coupon;
use App\Repositories\Interface\Coupon\CouponRepositoryInterface;

use function App\Helpers\errorResponse;

class CouponService
{
  public function __construct(private CouponRepositoryInterface $couponRepository) {}

  public function applyCoupon($couponId)
  {
    try {
      $coupon = $this->couponRepository->applyCoupon($couponId);

      return $this->isValidCoupon($coupon) ? __('app.messages.coupon.apply_coupon') : __('app.messages.coupon.coupon_not_valid');
    } catch (CouponException $e) {
      return errorResponse($e->getMessage(), $e->getCode());
    }
  }

  public function isValidCoupon($coupon)
  {
    return (
      $coupon->status === Coupon::ACTIVE
      && $coupon->start_date <= now()
      && $coupon->end_date >= now()
      && $coupon->usage_limit_per_user <= $coupon->usage_limit
    );
  }
}
