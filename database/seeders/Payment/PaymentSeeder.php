<?php

namespace Database\Seeders\Payment;

use App\Models\Order\Order;
use App\Models\Payment\Payment;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PaymentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Payment::create([
            'order_id' => Order::random()->id,
            'payment_method' => 'cash',
            'transaction_id' => '1234567890',
            'amount' => 100,
            'status' => Payment::PENDING,
            'paid_at' => now(),
        ]);
    }
}
