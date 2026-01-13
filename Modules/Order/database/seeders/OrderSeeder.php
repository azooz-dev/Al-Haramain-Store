<?php

namespace Modules\Order\Database\Seeders;

use Modules\Coupon\Entities\Coupon\Coupon;
use Modules\Order\Entities\Order\Order;
use Modules\Order\Enums\OrderStatus;
use Modules\Payment\Enums\PaymentMethod;
use Modules\User\Entities\User;
use Modules\User\Entities\Address;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get required related records
        $user = User::first();
        $address = Address::first();
        $coupon = Coupon::first();

        // Skip if required records don't exist
        if (!$user || !$address) {
            $this->command?->warn('Skipping OrderSeeder: User or Address not found. Run UserDatabaseSeeder first.');
            return;
        }

        // Create specific orders for testing
        Order::create([
            'user_id' => $user->id,
            'address_id' => $address->id,
            'coupon_id' => $coupon?->id,
            'order_number' => '1234567890',
            'total_amount' => 100,
            'payment_method' => PaymentMethod::CASH_ON_DELIVERY->value,
            'status' => collect(OrderStatus::cases())->random(),
        ]);

        Order::create([
            'user_id' => $user->id,
            'address_id' => $address->id,
            'coupon_id' => $coupon?->id,
            'order_number' => '123123123',
            'total_amount' => 100,
            'payment_method' => PaymentMethod::CASH_ON_DELIVERY->value,
            'status' => collect(OrderStatus::cases())->random(),
        ]);
    }
}
