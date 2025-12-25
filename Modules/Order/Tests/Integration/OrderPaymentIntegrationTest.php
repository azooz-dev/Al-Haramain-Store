<?php

namespace Modules\Order\Tests\Integration;

use Tests\TestCase;
use Spatie\Permission\Models\Role;
use Modules\Order\Entities\Order\Order;
use Modules\Payment\Enums\PaymentMethod;
use Modules\Payment\Enums\PaymentStatus;
use Modules\Payment\Entities\Payment\Payment;
use Tests\Support\Builders\OrderTestDataBuilder;
use Modules\Payment\Contracts\PaymentServiceInterface;
use Mockery;
use Modules\Payment\DTOs\PaymentResult;
use Carbon\Carbon;

class OrderPaymentIntegrationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'admin']);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_order_creates_payment_record_for_credit_card(): void
    {
        // Arrange - Mock PaymentServiceInterface
        $paymentResult = new PaymentResult(
            success: true,
            transactionId: 'pi_test_1234567890',
            paymentMethod: PaymentMethod::CREDIT_CARD->value,
            amount: 100.00,
            paidAt: Carbon::now()
        );

        $paymentServiceMock = Mockery::mock(PaymentServiceInterface::class);
        $paymentServiceMock->shouldReceive('processPayment')
            ->once()
            ->andReturn($paymentResult);
        $paymentServiceMock->shouldReceive('createPayment')
            ->once()
            ->andReturnUsing(function ($orderId, $result) {
                return Payment::factory()->create([
                    'order_id' => $orderId,
                    'transaction_id' => $result->transactionId,
                    'status' => PaymentStatus::SUCCESS,
                    'amount' => $result->amount,
                ]);
            });

        // Bind the mock to the service container BEFORE any services are resolved
        $this->app->instance(PaymentServiceInterface::class, $paymentServiceMock);

        // Create test data
        $builder = OrderTestDataBuilder::create()
            ->withVerifiedUser()
            ->withProduct(['quantity' => 10], ['quantity' => 10, 'price' => 100.00])
            ->withPaymentMethod(PaymentMethod::CREDIT_CARD->value);

        $orderData = $builder->buildOrderData();
        $orderData['payment_intent_id'] = $paymentResult->transactionId; // Required for credit card payments

        // Act
        $response = $this->actingAs($builder->getUser(), 'sanctum')
            ->postJson('/api/orders', $orderData);

        // Assert
        $response->assertStatus(201);

        $order = Order::where('user_id', $builder->getUser()->id)->first();
        $this->assertNotNull($order);

        // Verify payment record was created
        $payment = Payment::where('order_id', $order->id)->first();
        $this->assertNotNull($payment, 'Payment record should be created for credit card orders');
        $this->assertEquals($paymentResult->transactionId, $payment->transaction_id);
        $this->assertEquals(PaymentStatus::SUCCESS, $payment->status);
        $this->assertEquals($paymentResult->amount, $payment->amount);
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
        $response = $this->actingAs($builder->getUser(), 'sanctum')
            ->postJson('/api/orders', $orderData);

        // Assert
        $response->assertStatus(201);

        $order = Order::where('user_id', $builder->getUser()->id)->first();
        $this->assertNotNull($order);

        // payment_method might be stored as enum or string, so compare values
        $paymentMethod = $order->payment_method instanceof PaymentMethod
            ? $order->payment_method->value
            : $order->payment_method;
        $this->assertEquals(PaymentMethod::CASH_ON_DELIVERY->value, $paymentMethod);
    }
}
