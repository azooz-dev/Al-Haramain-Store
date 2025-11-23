<?php

namespace App\Repositories\Interface\Order;

use App\Models\Order\Order;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

interface OrderRepositoryInterface
{
  public function store(array $data): Order;

  public function isDelivered(int $orderId): bool;

  public function show(int $orderId);

  public function countCouponUsage(int $couponId): int;

  public function countUserCouponUsage(int $couponId, int $userId): int;

  public function getAll(): Collection;

  public function findById(int $id): Order;

  public function update(int $id, array $data): Order;

  public function delete(int $id): bool;

  public function count(): int;

  public function countByStatus(string $status): int;

  public function getQueryBuilder(): Builder;

  public function updateStatus(int $id, string $status): Order;

  // Widget-specific methods
  public function getRevenueByDateRange(Carbon $start, Carbon $end): float;

  public function getRevenueByDateRangeGrouped(Carbon $start, Carbon $end): Collection;

  public function getOrdersCountByDateRange(Carbon $start, Carbon $end): int;

  public function getOrdersCountByDateRangeGrouped(Carbon $start, Carbon $end): Collection;

  public function getOrdersCountByStatus(string $status, ?Carbon $start = null, ?Carbon $end = null): int;

  public function getOrdersCountByStatusGrouped(Carbon $start, Carbon $end): Collection;

  public function getAverageOrderValue(Carbon $start, Carbon $end): float;

  public function getAverageOrderValueGrouped(Carbon $start, Carbon $end): Collection;

  public function getRecentOrders(int $limit = 15): Collection;
}
