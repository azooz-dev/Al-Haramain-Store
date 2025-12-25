<?php

namespace Tests\E2E;

use Tests\TestCase;
use Tests\Support\Builders\OrderTestDataBuilder;
use Modules\Order\Entities\Order\Order;
use Modules\Payment\Entities\Payment\Payment;
use Modules\Payment\Enums\PaymentMethod;
use Modules\Payment\Enums\PaymentStatus;
use Modules\Payment\Contracts\PaymentServiceInterface;
use Modules\Payment\DTOs\PaymentResult;
use Modules\Order\Enums\OrderStatus;
use Mockery;
use Carbon\Carbon;

class CompletePurchaseFlowTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        \Spatie\Permission\Models\Role::firstOrCreate(
            ['name' => 'super_admin', 'guard_name' => 'admin']
        );
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_complete_purchase_flow_with_stripe_payment(): void
    {
        // Arrange - Mock PaymentServiceInterface to avoid real Stripe API calls
        $paymentResult = new PaymentResult(
            success: true,
            transactionId: 'pi_test_complete_flow_123',
            paymentMethod: PaymentMethod::CREDIT_CARD->value,
            amount: 150.00,
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
                    'payment_method' => PaymentMethod::CREDIT_CARD,
                ]);
            });

        // Bind the mock to the service container BEFORE any services are resolved
        $this->app->instance(PaymentServiceInterface::class, $paymentServiceMock);

        // Step 1: Create test data - User with product
        $builder = OrderTestDataBuilder::create()
            ->withVerifiedUser()
            ->withProduct(['quantity' => 10], ['quantity' => 10, 'price' => 150.00])
            ->withPaymentMethod(PaymentMethod::CREDIT_CARD->value);

        $orderData = $builder->buildOrderData();
        $orderData['payment_intent_id'] = $paymentResult->transactionId; // Required for credit card payments

        // Step 2: User creates order via API
        $orderResponse = $this->actingAs($builder->getUser(), 'sanctum')
            ->postJson('/api/orders', $orderData);

        // Step 3: Verify order was created successfully
        $orderResponse->assertStatus(201);
        $orderResponse->assertJsonStructure([
            'data' => [
                'identifier',
                'orderNumber',
                'status',
                'totalAmount',
            ],
        ]);

        $orderId = $orderResponse->json('data.identifier');
        $order = Order::find($orderId);

        // Step 4: Verify order details
        $this->assertNotNull($order, 'Order should be created');
        $this->assertEquals($builder->getUser()->id, $order->user_id, 'Order should belong to the user');
        $this->assertEquals(OrderStatus::PENDING, $order->status, 'Order should be in pending status');

        // Verify payment method
        $paymentMethod = $order->payment_method instanceof PaymentMethod
            ? $order->payment_method->value
            : $order->payment_method;
        $this->assertEquals(PaymentMethod::CREDIT_CARD->value, $paymentMethod, 'Order should have credit card payment method');

        // Step 5: Verify payment was processed and recorded
        $payment = Payment::where('order_id', $order->id)->first();
        $this->assertNotNull($payment, 'Payment record should be created for Stripe payments');
        $this->assertEquals($paymentResult->transactionId, $payment->transaction_id, 'Payment should have correct transaction ID');
        $this->assertEquals(PaymentStatus::SUCCESS, $payment->status, 'Payment should be successful');
        $this->assertEquals($paymentResult->amount, (float)$payment->amount, 'Payment amount should match');

        // Step 6: Verify order items were created
        $this->assertGreaterThan(0, $order->items->count(), 'Order should have items');

        // Step 7: Verify stock was deducted
        $variant = $builder->getProducts()[0]['variant'];
        $variant->refresh();
        $this->assertLessThan(10, $variant->quantity, 'Stock should be deducted after order creation');
    }
}
