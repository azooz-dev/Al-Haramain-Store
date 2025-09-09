<?php

namespace App\Repositories\Eloquent\Order\OrderItem;

use App\Models\Order\OrderItem;
use Illuminate\Support\Facades\DB;
use App\Repositories\Interface\Order\OrderItem\OrderItemRepositoryInterface;

class OrderItemRepository implements OrderItemRepositoryInterface
{
  /**
   * Bulk insert order items (assumes $itemsPayload items contain 'order_id' or we will add it)
   *
   * @param array $itemsPayload  array of ['variant_id','product_id','quantity','total_price']
   * @param int $orderId
   * @return bool
   */
  public function createMany(array $itemsPayload, int $orderId): bool
  {
    if (empty($itemsPayload)) {
      return true;
    }


    foreach ($itemsPayload as &$payload) {
      $payload['order_id'] = $orderId;
      $payload['created_at'] = now();
      $payload['updated_at'] = now();
      unset($payload['variant_id']);
    }
    unset($payload);

    // Use DB insert for performance
    return DB::table((new OrderItem())->getTable())->insert($itemsPayload);
  }
}
