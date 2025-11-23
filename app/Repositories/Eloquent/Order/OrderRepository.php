<?php

namespace App\Repositories\Eloquent\Order;

use App\Models\Order\Order;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use App\Repositories\Interface\Order\OrderRepositoryInterface;

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
      ->whereNotIn('status', [Order::CANCELLED, Order::REFUNDED])
      ->count();
  }

  public function countUserCouponUsage(int $couponId, int $userId): int
  {
    return Order::where('coupon_id', $couponId)
      ->where('user_id', $userId)
      ->whereNotIn('status', [Order::CANCELLED, Order::REFUNDED])
      ->count();
  }

  public function isDelivered(int $orderId): bool
  {
    return Order::where('id', $orderId)
      ->where('status', Order::DELIVERED)
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

  // Widget-specific methods
  public function getRevenueByDateRange(Carbon $start, Carbon $end): float
  {
    return Order::where('status', '!=', Order::CANCELLED)
      ->where('status', '!=', Order::REFUNDED)
      ->whereBetween('created_at', [$start, $end])
      ->sum('total_amount');
  }

  public function getRevenueByDateRangeGrouped(Carbon $start, Carbon $end): Collection
  {
    return Order::where('status', '!=', Order::CANCELLED)
      ->where('status', '!=', Order::REFUNDED)
      ->whereBetween('created_at', [$start, $end])
      ->selectRaw('DATE(created_at) as date, SUM(total_amount) as revenue')
      ->groupBy('date')
      ->orderBy('date')
      ->get();
  }

  public function getOrdersCountByDateRange(Carbon $start, Carbon $end): int
  {
    return Order::whereBetween('created_at', [$start, $end])->count();
  }

  public function getOrdersCountByDateRangeGrouped(Carbon $start, Carbon $end): Collection
  {
    return Order::whereBetween('created_at', [$start, $end])
      ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
      ->groupBy('date')
      ->orderBy('date')
      ->get();
  }

  public function getOrdersCountByStatus(string $status, ?Carbon $start = null, ?Carbon $end = null): int
  {
    $query = Order::where('status', $status);

    if ($start && $end) {
      $query->whereBetween('created_at', [$start, $end]);
    }

    return $query->count();
  }

  public function getOrdersCountByStatusGrouped(Carbon $start, Carbon $end): Collection
  {
    return Order::whereBetween('created_at', [$start, $end])
      ->select('status', DB::raw('COUNT(*) as count'))
      ->groupBy('status')
      ->get();
  }

  public function getAverageOrderValue(Carbon $start, Carbon $end): float
  {
    $orders = Order::where('status', '!=', Order::CANCELLED)
      ->where('status', '!=', Order::REFUNDED)
      ->whereBetween('created_at', [$start, $end]);

    $totalRevenue = $orders->sum('total_amount');
    $totalOrders = $orders->count();

    return $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;
  }

  public function getAverageOrderValueGrouped(Carbon $start, Carbon $end): Collection
  {
    return Order::where('status', '!=', Order::CANCELLED)
      ->where('status', '!=', Order::REFUNDED)
      ->whereBetween('created_at', [$start, $end])
      ->selectRaw('DATE(created_at) as date, AVG(total_amount) as avg_value')
      ->groupBy('date')
      ->orderBy('date')
      ->get();
  }

  public function getRecentOrders(int $limit = 15): Collection
  {
    return Order::with([
      'user',
      'items.orderable' => function ($morphTo) {
        $morphTo->morphWith([
          \App\Models\Product\Product::class => ['translations'],
          \App\Models\Offer\Offer::class => ['translations'],
        ]);
      },
      'items.variant',
      'items.color',
      'payments',
    ])
      ->latest()
      ->limit($limit)
      ->get();
  }
}
