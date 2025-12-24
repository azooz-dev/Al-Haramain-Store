<?php

namespace Modules\Payment\Tests\Feature;

use Tests\TestCase;
use Modules\Order\Entities\Order\Order;
use Modules\Payment\Entities\Payment\Payment;
use Modules\Payment\Enums\PaymentStatus;
use Modules\User\Entities\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * TC-PAY-008: Multiple Payment Attempts Recorded
 */
class PaymentHistoryTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        \Spatie\Permission\Models\Role::firstOrCreate(
            ['name' => 'super_admin', 'guard_name' => 'admin']
        );
    }

    public function test_records_multiple_payment_attempts(): void
    {
        // Arrange
        $order = Order::factory()->create();
        
        // First payment attempt (failed)
        $failedPayment = Payment::factory()->failed()->create([
            'order_id' => $order->id,
            'transaction_id' => 'txn_failed_123',
        ]);

        // Second payment attempt (succeeded)
        $successfulPayment = Payment::factory()->successful()->create([
            'order_id' => $order->id,
            'transaction_id' => 'txn_success_456',
        ]);

        // Act
        $payments = Payment::where('order_id', $order->id)->get();

        // Assert
        $this->assertCount(2, $payments);
        $this->assertEquals(PaymentStatus::FAILED, $failedPayment->status);
        $this->assertEquals(PaymentStatus::SUCCESS, $successfulPayment->status);
        
        // Latest payment should be successful
        $latestPayment = Payment::where('order_id', $order->id)
            ->latest()
            ->first();
        $this->assertEquals(PaymentStatus::SUCCESS, $latestPayment->status);
    }
}

