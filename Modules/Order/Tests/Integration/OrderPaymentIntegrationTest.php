<?php

namespace Modules\Order\Tests\Integration;

use Tests\TestCase;
use Tests\Support\Builders\OrderTestDataBuilder;
use Modules\Order\Entities\Order\Order;
use Modules\Payment\Entities\Payment\Payment;
use Modules\Payment\Enums\PaymentMethod;
use Modules\Payment\Enums\PaymentStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OrderPaymentIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        \Spatie\Permission\Models\Role::firstOrCreate(
            ['name' => 'super_admin', 'guard_name' => 'admin']
        );
    }

    public function test_order_creates_payment_record_for_credit_card(): void
    {
        // Arrange
        $builder = OrderTestDataBuilder::create()
            ->withVerifiedUser()
            ->withProduct(['quantity' => 10], ['quantity' => 10])
            ->withPaymentMethod(PaymentMethod::CREDIT_CARD->value);

        $orderData = $builder->buildOrderData();
        $orderData['payment_intent_id'] = 'pi_test_123';

        // Act
        $response = $this->postJson('/api/orders', $orderData);

        // Assert
        $response->assertStatus(201);
        
        $order = Order::where('user_id', $builder->getUser()->id)->first();
        $this->assertNotNull($order);
        $this->assertEquals(PaymentMethod::CREDIT_CARD->value, $order->payment_method);
    }

    public function test_order_with_cod_does_not_require_payment_record(): void
    {
        // Arrange
        $builder = OrderTestDataBuilder::create()
            ->withVerifiedUser()
            ->withProduct(['quantity' => 10], ['quantity' => 10])
            ->withPaymentMethod(PaymentMethod::CASH_ON_DELIVERY->value);

        $orderData = $builder->buildOrderData();

        // Act
        $response = $this->postJson('/api/orders', $orderData);

        // Assert
        $response->assertStatus(201);
        
        $order = Order::where('user_id', $builder->getUser()->id)->first();
        $this->assertNotNull($order);
        $this->assertEquals(PaymentMethod::CASH_ON_DELIVERY->value, $order->payment_method);
    }
}

