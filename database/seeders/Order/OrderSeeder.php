<?php

namespace Database\Seeders\Order;

use App\Models\Coupon\Coupon;
use App\Models\Order\Order;
use App\Models\User\User;
use App\Models\User\UserAddresses\Address;
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
            'user_id' => User::first()->id,
            'address_id' => Address::first()->id,
            'coupon_id' => Coupon::first()->id,
            'order_number' => '1234567890',
            'total_amount' => 100,
            'payment_method' => Order::PAYMENT_METHOD_CASH_ON_DELIVERY,
            'status' => collect([Order::PENDING, Order::PROCESSING, Order::SHIPPED, Order::DELIVERED, Order::CANCELLED, Order::REFUNDED])->random(),
        ]);

        Order::create([
            'user_id' => User::first()->id,
            'address_id' => Address::first()->id,
            'coupon_id' => Coupon::first()->id,
            'order_number' => '123123123',
            'total_amount' => 100,
            'payment_method' => Order::PAYMENT_METHOD_CASH_ON_DELIVERY,
            'status' => collect([Order::PENDING, Order::PROCESSING, Order::SHIPPED, Order::DELIVERED, Order::CANCELLED, Order::REFUNDED])->random(),
        ]);
    }
}
