<?php

namespace Modules\Payment\Tests\Unit\Processors;

use Tests\TestCase;
use Modules\Payment\Services\Payment\Processors\StripePaymentProcessor;
use Modules\Payment\Exceptions\Payment\ProcessPaymentException;
use Mockery;

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
        $orderData = ['total_amount' => 100.00]; // Missing payment_intent_id

        // Act & Assert
        $this->expectException(ProcessPaymentException::class);

        try {
            $this->processor->processPayment($orderData);
            $this->fail('Expected ProcessPaymentException was not thrown');
        } catch (ProcessPaymentException $e) {
            // The exception message should indicate that payment_intent_id is required
            // It might be translated, so check for common keywords
            $message = strtolower($e->getMessage());
            $this->assertTrue(
                str_contains($message, 'payment') ||
                    str_contains($message, 'intent') ||
                    str_contains($message, 'required'),
                "Exception message should mention payment intent requirement. Got: {$e->getMessage()}"
            );
            throw $e; // Re-throw to satisfy expectException
        }
    }
}
