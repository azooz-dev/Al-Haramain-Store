<?php

namespace Modules\Payment\Tests\Unit\Processors;

use Tests\TestCase;
use Modules\Payment\Services\Payment\Processors\StripePaymentProcessor;
use Modules\Payment\DTOs\PaymentResult;
use Modules\Payment\Exceptions\Payment\CreatePaymentIntentException;
use Modules\Payment\Exceptions\Payment\ProcessPaymentException;
use Modules\Payment\Exceptions\Payment\VerifyPaymentException;
use Mockery;

/**
 * TC-PAY-001: Create Payment Intent - Stripe
 * TC-PAY-002: Process Payment - Stripe Success
 * TC-PAY-003: Process Payment - Stripe Failure
 */
class StripePaymentProcessorTest extends TestCase
{
    private StripePaymentProcessor $processor;

    protected function setUp(): void
    {
        parent::setUp();
        $this->processor = new StripePaymentProcessor();
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_requires_payment_intent_returns_true(): void
    {
        // Act
        $result = $this->processor->requiresPaymentIntent();

        // Assert
        $this->assertTrue($result);
    }

    public function test_processes_payment_requires_payment_intent_id(): void
    {
        // Arrange
        $orderData = ['total_amount' => 100.00];

        // Act & Assert
        $this->expectException(ProcessPaymentException::class);

        $this->processor->processPayment($orderData);
    }
}

