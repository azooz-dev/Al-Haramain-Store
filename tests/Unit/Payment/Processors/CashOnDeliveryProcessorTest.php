<?php

namespace Tests\Unit\Payment\Processors;

use Tests\TestCase;
use Modules\Payment\Enums\PaymentMethod;
use Modules\Payment\Services\Payment\Processors\CashOnDeliveryProcessor;
use Modules\Payment\Exceptions\Payment\VerifyPaymentException;

/**
 * Unit tests for CashOnDeliveryProcessor
 */
class CashOnDeliveryProcessorTest extends TestCase
{
    private CashOnDeliveryProcessor $processor;

    protected function setUp(): void
    {
        parent::setUp();
        $this->processor = app(CashOnDeliveryProcessor::class);
    }

    /**
     * Test cash on delivery payment processing
     */
    public function test_cash_on_delivery_payment_processing(): void
    {
        // Arrange
        $orderData = [
            'payment_method' => PaymentMethod::CASH_ON_DELIVERY->value,
            'total_amount' => 150.00,
        ];

        // Act
        $result = $this->processor->processPayment($orderData);

        // Assert
        $this->assertInstanceOf(\Modules\Payment\DTOs\PaymentResult::class, $result);
        $this->assertTrue($result->success);
        $this->assertEquals(PaymentMethod::CASH_ON_DELIVERY->value, $result->paymentMethod);
        $this->assertEquals(150.00, $result->amount);
        $this->assertNull($result->transactionId);
    }

    /**
     * Test payment intent creation returns null
     */
    public function test_payment_intent_creation_returns_null(): void
    {
        // Arrange
        $orderData = [
            'payment_method' => PaymentMethod::CASH_ON_DELIVERY->value,
            'total_amount' => 100.00,
        ];

        // Act
        $result = $this->processor->createPaymentIntent($orderData);

        // Assert
        $this->assertNull($result);
    }

    /**
     * Test verify payment throws exception
     */
    public function test_verify_payment_throws_exception(): void
    {
        // Act & Assert
        $this->expectException(VerifyPaymentException::class);
        $this->processor->verifyPayment('test_intent_id');
    }
}
