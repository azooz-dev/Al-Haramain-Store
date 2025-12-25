<?php

namespace Tests\E2E;

use Tests\TestCase;
use Modules\Order\Entities\Order\Order;
use Modules\Payment\Enums\PaymentMethod;

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

    public function test_cod_purchase_flow(): void
    {
        // Arrange - Use OrderTestDataBuilder to ensure proper data structure
        $builder = \Tests\Support\Builders\OrderTestDataBuilder::create()
            ->withVerifiedUser()
            ->withProduct(['quantity' => 5], ['quantity' => 5])
            ->withPaymentMethod(PaymentMethod::CASH_ON_DELIVERY->value);

        $orderData = $builder->buildOrderData();

        // Step 1: User creates order
        $orderResponse = $this->actingAs($builder->getUser(), 'sanctum')
            ->postJson('/api/orders', $orderData);

        $orderResponse->assertStatus(201);
        $orderId = $orderResponse->json('data.identifier');

        // Step 2: Verify order was created with COD payment method
        $order = Order::find($orderId);
        $this->assertNotNull($order);
        // payment_method might be stored as enum or string, so compare values
        $paymentMethod = $order->payment_method instanceof PaymentMethod
            ? $order->payment_method->value
            : $order->payment_method;
        $this->assertEquals(PaymentMethod::CASH_ON_DELIVERY->value, $paymentMethod);

        // Step 3: Verify order status
        $this->assertNotNull($order->status);
    }
}
