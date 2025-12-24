<?php

namespace Modules\Payment\Tests\Unit\Processors;

use Tests\TestCase;
use Modules\Payment\Services\Payment\Processors\CashOnDeliveryProcessor;
use Modules\Payment\Exceptions\Payment\VerifyPaymentException;
use Modules\Payment\DTOs\PaymentResult;

/**
 * TC-PAY-006: COD Order Creation
 */
class CashOnDeliveryProcessorTest extends TestCase
{
    private CashOnDeliveryProcessor $processor;

    protected function setUp(): void
    {
        parent::setUp();
        $this->processor = new CashOnDeliveryProcessor();
    }

    public function test_requires_payment_intent_returns_false(): void
    {
        // Act
        $result = $this->processor->requiresPaymentIntent();

        // Assert
        $this->assertFalse($result);
    }

    public function test_create_payment_intent_returns_null(): void
    {
        // Arrange
        $orderData = [
            'user_id' => 1,
            'address_id' => 1,
            'total_amount' => 100.00,
            'payment_method' => 'cash_on_delivery',
            'items' => [],
        ];

        // Act
        $result = $this->processor->createPaymentIntent($orderData);

        // Assert
        $this->assertNull($result);
    }

    public function test_process_payment_returns_successful_result(): void
    {
        // Arrange
        $orderData = [
            'user_id' => 1,
            'address_id' => 1,
            'total_amount' => 100.00,
            'payment_method' => 'cash_on_delivery',
            'items' => [],
        ];

        // Act
        $result = $this->processor->processPayment($orderData);

        // Assert
        $this->assertInstanceOf(PaymentResult::class, $result);
        $this->assertTrue($result->success);
        $this->assertEquals(100.00, $result->amount);
        $this->assertNull($result->transactionId);
    }

    public function test_verify_payment_throws_exception(): void
    {
        // Arrange
        $paymentIntentId = 'pi_test_123';

        // Act & Assert
        $this->expectException(VerifyPaymentException::class);
        $this->processor->verifyPayment($paymentIntentId);
    }
}

