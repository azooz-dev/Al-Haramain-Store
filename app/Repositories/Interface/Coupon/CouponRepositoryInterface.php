<?php

namespace App\Repositories\Interface\Coupon;

use App\Models\Coupon\Coupon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

interface CouponRepositoryInterface
{
  public function findCoupon(string $couponCode);

  public function getAll(): Collection;

  public function findById(int $id): Coupon;

  public function create(array $data): Coupon;

  public function update(int $id, array $data): Coupon;

  public function delete(int $id): bool;

  public function count(): int;

  public function getQueryBuilder(): Builder;

  public function codeExists(string $code, ?int $excludeId = null): bool;
}
