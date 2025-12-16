<?php

namespace Tests\Feature\Payment;

use Tests\TestCase;
use Modules\Order\Entities\Order\Order;
use App\Services\Payment\PaymentService;
use App\Services\Payment\Processors\CashOnDeliveryProcessor;
use App\Services\Payment\Processors\StripePaymentProcessor;
use App\Exceptions\Payment\ProcessPaymentException;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * Enterprise-grade Payment Processing Tests
 */
class PaymentProcessingTest extends TestCase
{
    use RefreshDatabase;

    private PaymentService $paymentService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->paymentService = app(PaymentService::class);
    }

    /**
     * Test cash on delivery payment processing succeeds
     * 
     */
    public function test_cash_on_delivery_payment_processing_succeeds(): void
    {
        // Arrange
        $orderData = [
            'payment_method' => Order::PAYMENT_METHOD_CASH_ON_DELIVERY,
            'total_amount' => 250.75,
            'user_id' => 1,
            'address_id' => 1,
        ];

        // Act
        $result = $this->paymentService->processPayment($orderData);

        // Assert
        $this->assertInstanceOf(\App\DTOs\PaymentResult::class, $result);
        $this->assertTrue($result->success);
        $this->assertEquals(Order::PAYMENT_METHOD_CASH_ON_DELIVERY, $result->paymentMethod);
        $this->assertEquals(250.75, $result->amount);
        $this->assertNull($result->transactionId);
        $this->assertNotNull($result->paidAt);
    }

    /**
     * Test payment intent creation returns null for cash on delivery
     * 
     */
    public function test_payment_intent_creation_returns_null_for_cash_on_delivery(): void
    {
        // Arrange
        $orderData = [
            'payment_method' => Order::PAYMENT_METHOD_CASH_ON_DELIVERY,
            'total_amount' => 100.00,
        ];

        // Act
        $result = $this->paymentService->createPaymentIntent($orderData);

        // Assert
        $this->assertNull($result);
    }

    /**
     * Test payment processing fails when payment intent ID is missing
     * 
     */
    public function test_payment_processing_fails_when_payment_intent_id_is_missing(): void
    {
        // Arrange
        $orderData = [
            'payment_method' => Order::PAYMENT_METHOD_CREDIT_CARD,
            'payment_intent_id' => null,
            'total_amount' => 100.00,
        ];

        // Act & Assert
        $this->expectException(ProcessPaymentException::class);
        $this->paymentService->processPayment($orderData);
    }

    /**
     * Test cash on delivery processor requires payment intent returns false
     * 
     */
    public function test_cash_on_delivery_processor_requires_payment_intent_returns_false(): void
    {
        // Arrange
        $processor = app(CashOnDeliveryProcessor::class);

        // Act
        $result = $processor->requiresPaymentIntent();

        // Assert
        $this->assertFalse($result);
    }

    /**
     * Test stripe payment processor requires payment intent returns true
     * 
     */
    public function test_stripe_payment_processor_requires_payment_intent_returns_true(): void
    {
        // Arrange
        $processor = app(StripePaymentProcessor::class);

        // Act
        $result = $processor->requiresPaymentIntent();

        // Assert
        $this->assertTrue($result);
    }
}
