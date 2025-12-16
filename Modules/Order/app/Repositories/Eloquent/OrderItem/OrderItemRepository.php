<?php

namespace Modules\Order\Repositories\Eloquent\OrderItem;

use Modules\Order\Entities\OrderItem\OrderItem;
use Illuminate\Support\Facades\DB;
use Modules\Order\Repositories\Interface\OrderItem\OrderItemRepositoryInterface;

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
    foreach ($itemsPayload as &$payload) {
      $payload['order_id'] = $orderId;
      $payload['created_at'] = now();
      $payload['updated_at'] = now();
    }
    unset($payload);

    // Use DB insert for performance
    return DB::table((new OrderItem())->getTable())->insert($itemsPayload);
  }

  public function update(int $itemId, array $data): bool
  {
    return OrderItem::where('id', $itemId)->update($data);
  }

  public function checkItemIsReviewed($itemId): bool
  {
    return OrderItem::where('id', $itemId)->where('is_reviewed', true)->exists();
  }
}
