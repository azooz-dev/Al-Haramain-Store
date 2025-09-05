<?php

namespace App\Repositories\Eloquent\Order\OrderItem;

use App\Models\Order\OrderItem;
use App\Models\Product\ProductVariant;
use App\Repositories\interface\Order\OrderItem\OrderItemRepositoryInterface;

class OrderItemRepository implements OrderItemRepositoryInterface
{
  public function store(array $data, $orderId): OrderItem
  {
    $variant = ProductVariant::find($data['variant_id']);

    return OrderItem::create([
      'order_id' => $orderId,
      'product_id' => $variant->product_id,
      'quantity' => $data['quantity'],
      'total_price' => isset($variant->amount_discount_price) ? $data['quantity'] * $variant->amount_discount_price : $data['quantity'] * $variant->price,
    ]);
  }
}
