<?php

namespace App\Services\Coupon;

use Carbon\Carbon;
use App\Models\Coupon\Coupon;
use function App\Helpers\errorResponse;

use App\Exceptions\Order\OrderException;
use App\Exceptions\Coupon\CouponException;
use App\Repositories\Interface\Order\OrderRepositoryInterface;
use App\Repositories\Interface\Coupon\CouponRepositoryInterface;

class CouponService
{
  public function __construct(
    private CouponRepositoryInterface $couponRepository,
    private OrderRepositoryInterface $orderRepository
  ) {}

  /**
   * Validate coupon and return new total amount after discount
   *
   * @throws OrderException
   */
  public function applyCouponToOrder(int $couponId, float $totalAmount, int $userId): float
  {
    $coupon = $this->couponRepository->findCoupon($couponId);
    if (!$coupon) {
      throw new OrderException(__('app.messages.order.coupon_not_found'), 404);
    }

    if ($coupon->status === Coupon::INACTIVE) {
      throw new OrderException(__('app.messages.order.coupon_inactive'), 400);
    }

    $now = Carbon::now();
    if ($coupon->start_date && $now->lt($coupon->start_date)) {
      throw new OrderException(__('app.messages.order.coupon_not_started'), 400);
    }
    if ($coupon->end_date && $now->gt($coupon->end_date)) {
      throw new OrderException(__('app.messages.order.coupon_expired'), 400);
    }

    // Global usage limit
    $usedCount = $this->orderRepository->countCouponUsage($coupon->id);
    if ($coupon->usage_limit !== null && $usedCount >= $coupon->usage_limit) {
      throw new OrderException(__('app.messages.order.coupon_usage_limit_exceeded', ['usage_limit' => $coupon->usage_limit]), 400);
    }

    // Per-user usage limit
    $userUsed = $this->orderRepository->countUserCouponUsage($coupon->id, $userId);
    if ($coupon->usage_limit_per_user !== null && $userUsed >= $coupon->usage_limit_per_user) {
      throw new OrderException(__('app.messages.order.coupon_usage_limit_per_user_exceeded', ['limit' => $coupon->usage_limit_per_user]), 400);
    }

    // Compute discount
    if ($coupon->discount_type === Coupon::FIXED) {
      $discountAmount = (float)$coupon->discount_amount;
    } else {
      $discountAmount = ($coupon->discount_amount / 100.0) * $totalAmount;
    }

    // Ensure not negative, clamp
    $newTotal = max(0.0, round($totalAmount - $discountAmount, 2));

    return $newTotal;
  }
}
