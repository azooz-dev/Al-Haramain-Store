<?php

namespace Modules\Coupon\Services\Coupon;

use Carbon\Carbon;
use Modules\Coupon\Entities\Coupon\Coupon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Modules\Order\Exceptions\Order\OrderException;
use Modules\Coupon\Exceptions\Coupon\CouponException;
use Modules\Coupon\Contracts\CouponServiceInterface;
use Modules\Order\Repositories\Interface\Order\OrderRepositoryInterface;
use Modules\Coupon\Repositories\Interface\Coupon\CouponRepositoryInterface;

class CouponService implements CouponServiceInterface
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
  public function applyCouponToOrder(string $couponCode, float $totalAmount, int $userId): float
  {
    $coupon = $this->applyCoupon($couponCode, $userId);
    // Compute discount
    if ($coupon->type === Coupon::FIXED) {
      $discountAmount = (float)$coupon->discount_amount;
    } else {
      $discountAmount = ($coupon->discount_amount / 100.0) * $totalAmount;
    }

    // Ensure not negative, clamp
    $newTotal = max(0.0, round($totalAmount - $discountAmount, 2));

    $coupon->couponUsers()->updateOrCreate(
      ['user_id' => $userId],
      ['times_used' => DB::raw('COALESCE(times_used,0) + 1')]
    );

    return $newTotal;
  }

  public function applyCoupon(string $couponCode, int $userId)
  {
    $coupon = $this->couponRepository->findCoupon($couponCode);
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

    return $coupon;
  }

  public function createCoupon(array $data): Coupon
  {
    // Validate code uniqueness
    if ($this->couponRepository->codeExists($data['code'])) {
      throw new CouponException(__('app.messages.coupon.code_exists'), 422);
    }

    // Create coupon via repository
    $coupon = $this->couponRepository->create($data);

    // Return coupon with relationships loaded
    return $coupon->fresh(['couponUsers']);
  }

  public function updateCoupon(int $id, array $data): Coupon
  {
    $coupon = $this->couponRepository->findById($id);

    // Validate code uniqueness if code changed
    if (isset($data['code']) && $data['code'] !== $coupon->code) {
      if ($this->couponRepository->codeExists($data['code'], $id)) {
        throw new CouponException(__('app.messages.coupon.code_exists'), 422);
      }
    }

    // Update coupon via repository
    $coupon = $this->couponRepository->update($id, $data);

    // Return updated coupon with relationships loaded
    return $coupon;
  }

  public function deleteCoupon(int $id): bool
  {
    $coupon = $this->couponRepository->findById($id);

    // Check if coupon can be deleted (no usage)
    if (!$this->canDeleteCoupon($coupon)) {
      throw new CouponException(__('app.messages.coupon.must_be_empty'), 422);
    }

    return $this->couponRepository->delete($id);
  }

  public function toggleCouponStatus(int $id): Coupon
  {
    $coupon = $this->couponRepository->findById($id);

    $newStatus = $coupon->status === Coupon::ACTIVE ? Coupon::INACTIVE : Coupon::ACTIVE;

    return $this->couponRepository->update($id, ['status' => $newStatus]);
  }

  public function activateCoupons(array $ids): int
  {
    return Coupon::whereIn('id', $ids)->update(['status' => Coupon::ACTIVE]);
  }

  public function deactivateCoupons(array $ids): int
  {
    return Coupon::whereIn('id', $ids)->update(['status' => Coupon::INACTIVE]);
  }

  public function getCouponsCount(): int
  {
    return $this->couponRepository->count();
  }

  public function getQueryBuilder(): Builder
  {
    return $this->couponRepository->getQueryBuilder();
  }

  public function getRemainingUses(Coupon $coupon): int|string
  {
    $limit = $coupon->usage_limit;

    if ($limit === null) {
      return __('app.common.unlimited');
    }

    $used = $this->getTotalUsage($coupon);

    return max($limit - $used, 0);
  }

  public function canDeleteCoupon(Coupon $coupon): bool
  {
    return $coupon->couponUsers->count() === 0;
  }

  public function getTotalUsage(Coupon $coupon): int
  {
    // Use withSum result if available, otherwise calculate from relationship
    if (isset($coupon->coupon_users_sum_times_used)) {
      return (int) ($coupon->coupon_users_sum_times_used ?? 0);
    }

    return (int) $coupon->couponUsers()->sum('times_used');
  }
}
