<?php

namespace Database\Seeders\Order;

use App\Models\Order\Order;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Order::create([
            'user_id' => 1,
            'address_id' => 1,
            'order_number' => '1234567890',
            'total_amount' => 100,
            'payment_method' => 'cash',
            'status' => rand(Order::PENDING, Order::REFUNDED, Order::CANCELLED, Order::DELIVERED, Order::PROCESSING, Order::SHIPPED),
        ]);
    }
}
