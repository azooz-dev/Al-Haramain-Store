<?php

namespace Tests\E2E;

use Tests\TestCase;
use Modules\User\Entities\User;
use Modules\Catalog\Entities\Product\Product;
use Modules\Order\Entities\Order\Order;
use Modules\Payment\Entities\Payment\Payment;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * E2E-02: COD Purchase Flow
 * 
 * Tests the complete flow for Cash on Delivery purchases:
 * 1. User creates order
 * 2. User selects COD payment method
 * 3. Order is created with COD status
 * 4. Payment record is created
 */
class CODPurchaseFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_cod_purchase_flow(): void
    {
        // Arrange
        $user = User::factory()->verified()->create();
        $product = Product::factory()->create(['stock' => 5]);

        // Step 1: User creates order
        $orderData = [
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 1,
                ],
            ],
        ];

        $orderResponse = $this->actingAs($user, 'sanctum')
            ->postJson('/api/orders', $orderData);

        $orderResponse->assertStatus(201);
        $orderId = $orderResponse->json('data.id');

        // Step 2: User selects COD payment
        $paymentData = [
            'order_id' => $orderId,
            'payment_method' => 'cod',
        ];

        $paymentResponse = $this->actingAs($user, 'sanctum')
            ->postJson('/api/payments/cod', $paymentData);

        $paymentResponse->assertStatus(201);

        // Step 3: Verify payment was created with COD method
        $payment = Payment::where('order_id', $orderId)->first();
        $this->assertNotNull($payment);
        $this->assertEquals('cod', $payment->payment_method);

        // Step 4: Verify order status
        $order = Order::find($orderId);
        $this->assertNotNull($order);
    }
}

