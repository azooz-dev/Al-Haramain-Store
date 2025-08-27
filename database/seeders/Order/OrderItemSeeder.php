<?php

namespace Database\Seeders\Order;

use App\Models\Order\Order;
use App\Models\Order\OrderItem;
use App\Models\Product\Product;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class OrderItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        OrderItem::create([
            'order_id' => Order::first()->id,
            'product_id' => Product::first()->id,
            'quantity' => 1,
            'total_price' => 100,
            'amount_discount_price' => 100,
        ]);
    }
}
