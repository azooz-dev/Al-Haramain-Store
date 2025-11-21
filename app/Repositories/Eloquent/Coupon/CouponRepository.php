<?php

namespace App\Repositories\Eloquent\Coupon;

use App\Models\Coupon\Coupon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use App\Repositories\Interface\Coupon\CouponRepositoryInterface;

class CouponRepository implements CouponRepositoryInterface
{
  public function findCoupon(string $couponCode)
  {
    return Coupon::where('code', $couponCode)->first();
  }

  public function getAll(): Collection
  {
    return Coupon::with(['couponUsers'])->get();
  }

  public function findById(int $id): Coupon
  {
    return Coupon::with(['couponUsers'])->findOrFail($id);
  }

  public function create(array $data): Coupon
  {
    return Coupon::create($data);
  }

  public function update(int $id, array $data): Coupon
  {
    $coupon = Coupon::findOrFail($id);
    $coupon->update($data);
    return $coupon->fresh(['couponUsers']);
  }

  public function delete(int $id): bool
  {
    $coupon = Coupon::findOrFail($id);
    return $coupon->delete();
  }

  public function count(): int
  {
    return Coupon::count();
  }

  public function getQueryBuilder(): Builder
  {
    return Coupon::query()
      ->with(['couponUsers'])
      ->withSum('couponUsers', 'times_used');
  }

  public function codeExists(string $code, ?int $excludeId = null): bool
  {
    $query = Coupon::where('code', $code);
    
    if ($excludeId !== null) {
      $query->where('id', '!=', $excludeId);
    }
    
    return $query->exists();
  }
}
