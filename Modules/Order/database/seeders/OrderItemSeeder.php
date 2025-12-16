<?php

namespace Modules\Order\Database\Seeders;

use Modules\Order\Entities\Order\Order;
use Modules\Order\Entities\OrderItem\OrderItem;
use Modules\Catalog\Entities\Product\Product;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class OrderItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create sample order items using factory
        OrderItem::factory(300)->create();

        // Create specific order item for testing
        OrderItem::create([
            'order_id' => Order::first()->id,
            'product_id' => Product::first()->id,
            'quantity' => 1,
            'total_price' => 100,
            'amount_discount_price' => 100,
        ]);
    }
}
