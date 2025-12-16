<?php

namespace Tests\Unit\Payment\Processors;

use Tests\TestCase;
use Modules\Payment\Services\Payment\Processors\StripePaymentProcessor;
use Modules\Payment\Exceptions\Payment\CreatePaymentIntentException;
use Modules\Payment\Exceptions\Payment\ProcessPaymentException;
use Modules\Payment\Exceptions\Payment\VerifyPaymentException;
use Mockery;

/**
 * Unit tests for StripePaymentProcessor
 * Note: These tests mock Stripe API calls
 */
class StripePaymentProcessorTest extends TestCase
{
    private StripePaymentProcessor $processor;

    protected function setUp(): void
    {
        parent::setUp();
        $this->processor = app(StripePaymentProcessor::class);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /**
     * Test requires payment intent returns true
     */
    public function test_requires_payment_intent_returns_true(): void
    {
        // Act
        $result = $this->processor->requiresPaymentIntent();

        // Assert
        $this->assertTrue($result);
    }

    /**
     * Test payment processing fails when payment intent ID is missing
     */
    public function test_payment_processing_fails_when_payment_intent_id_is_missing(): void
    {
        // Arrange
        $orderData = [
            'payment_method' => 'credit_card',
            'payment_intent_id' => null,
            'total_amount' => 100.00,
        ];

        // Act & Assert
        $this->expectException(ProcessPaymentException::class);
        $this->processor->processPayment($orderData);
    }
}
