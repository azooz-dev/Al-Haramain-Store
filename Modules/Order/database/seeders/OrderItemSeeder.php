<?php

namespace Modules\Order\Database\Seeders;

use Modules\Order\Entities\Order\Order;
use Modules\Order\Entities\OrderItem\OrderItem;
use Modules\Catalog\Entities\Product\Product;
use Illuminate\Database\Seeder;

class OrderItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create specific order item for testing
        $product = Product::first();

        if (!$product || !Order::first()) {
            return; // Skip if no product or order exists
        }

        OrderItem::create([
            'order_id' => Order::first()->id,
            'orderable_id' => $product->id,
            'orderable_type' => get_class($product),
            'quantity' => 1,
            'total_price' => 100,
            'amount_discount_price' => 100,
        ]);
    }
}
