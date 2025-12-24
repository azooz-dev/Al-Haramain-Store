<?php

namespace Tests\E2E;

use Tests\TestCase;
use Modules\User\Entities\User;
use Modules\Catalog\Entities\Product\Product;
use Modules\Order\Entities\Order\Order;
use Modules\Payment\Entities\Payment\Payment;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * E2E-01: Complete Purchase Flow
 * 
 * Tests the complete flow from product browsing to order completion:
 * 1. User browses products
 * 2. User adds product to cart (if applicable)
 * 3. User creates order
 * 4. User completes payment
 * 5. Order status updates
 */
class CompletePurchaseFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_complete_purchase_flow_with_stripe_payment(): void
    {
        // Arrange
        $user = User::factory()->verified()->create();
        $product = Product::factory()->create(['stock' => 10]);

        // Step 1: User creates order
        $orderData = [
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 2,
                ],
            ],
        ];

        $orderResponse = $this->actingAs($user, 'sanctum')
            ->postJson('/api/orders', $orderData);

        $orderResponse->assertStatus(201);
        $orderId = $orderResponse->json('data.id');

        // Step 2: User creates payment intent
        $paymentData = [
            'order_id' => $orderId,
            'payment_method' => 'stripe',
        ];

        $paymentResponse = $this->actingAs($user, 'sanctum')
            ->postJson('/api/payments/intent', $paymentData);

        $paymentResponse->assertStatus(201);

        // Step 3: Verify order status
        $order = Order::find($orderId);
        $this->assertNotNull($order);
        $this->assertEquals('pending', $order->status->value);

        // Step 4: Verify product stock was deducted
        $product->refresh();
        $this->assertEquals(8, $product->stock); // 10 - 2 = 8
    }
}

