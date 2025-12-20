<?php

namespace Modules\Payment\Database\Seeders\Payment;

use Modules\Order\Entities\Order\Order;
use Modules\Payment\Entities\Payment\Payment;
use Modules\Payment\Enums\PaymentStatus;
use Modules\Payment\Enums\PaymentMethod;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PaymentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create sample payments using factory
        Payment::factory(150)->create();

        // Create specific payment for testing
        Payment::create([
            'order_id' => Order::first()->id,
            'payment_method' => PaymentMethod::CASH_ON_DELIVERY,
            'transaction_id' => '1234567890',
            'amount' => 100,
            'status' => PaymentStatus::SUCCESS,
            'paid_at' => now(),
        ]);
    }
}
