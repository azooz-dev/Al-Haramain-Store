<?php

namespace Modules\Order\Repositories\Eloquent\Order;

use Modules\Order\Entities\Order\Order;
use Modules\Order\Enums\OrderStatus;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Modules\Order\Repositories\Interface\Order\OrderRepositoryInterface;

class OrderRepository implements OrderRepositoryInterface
{
  public function store(array $data): Order
  {
    return Order::create($data);
  }

  public function show(int $orderId): Order
  {
    return Order::findOrFail($orderId);
  }

  public function countCouponUsage(int $couponId): int
  {
    return Order::where('coupon_id', $couponId)
      ->whereNotIn('status', [OrderStatus::CANCELLED->value, OrderStatus::REFUNDED->value])
      ->count();
  }

  public function countUserCouponUsage(int $couponId, int $userId): int
  {
    return Order::where('coupon_id', $couponId)
      ->where('user_id', $userId)
      ->whereNotIn('status', [OrderStatus::CANCELLED->value, OrderStatus::REFUNDED->value])
      ->count();
  }

  public function isDelivered(int $orderId): bool
  {
    return Order::where('id', $orderId)
      ->where('status', OrderStatus::DELIVERED)
      ->exists();
  }

  public function getAll(): Collection
  {
    return Order::with([
      'user',
      'address',
      'coupon',
      'items.orderable.translations',
      'items.variant',
      'items.color',
      'payments',
    ])->get();
  }

  public function findById(int $id): Order
  {
    return Order::with([
      'user',
      'address',
      'coupon',
      'items.orderable.translations',
      'items.variant',
      'items.color',
      'payments',
    ])->findOrFail($id);
  }

  public function update(int $id, array $data): Order
  {
    $order = Order::findOrFail($id);
    $order->update($data);
    return $order->fresh([
      'user',
      'address',
      'coupon',
      'items.orderable.translations',
      'items.variant',
      'items.color',
      'payments',
    ]);
  }

  public function delete(int $id): bool
  {
    $order = Order::findOrFail($id);
    return $order->delete();
  }

  public function count(): int
  {
    return Order::count();
  }

  public function countByStatus(string $status): int
  {
    return Order::where('status', $status)->count();
  }

  public function getQueryBuilder(): Builder
  {
    return Order::query()
      ->with([
        'user',
        'address',
        'coupon',
        'items.orderable.translations',
        'items.variant',
        'items.color',
        'payments',
      ])
      ->withCount(['items']);
  }

  public function updateStatus(int $id, string $status): Order
  {
    $order = Order::findOrFail($id);
    $order->update(['status' => $status]);
    return $order->fresh([
      'user',
      'address',
      'coupon',
      'items.orderable.translations',
      'items.variant',
      'items.color',
      'payments',
    ]);
  }

  public function markOrdersAsProcessing(array $ids): int
  {
    return Order::whereIn('id', $ids)->update(['status' => OrderStatus::PROCESSING->value]);
  }

  public function markOrdersAsShipped(array $ids): int
  {
    return Order::whereIn('id', $ids)->update(['status' => OrderStatus::SHIPPED->value]);
  }
}
