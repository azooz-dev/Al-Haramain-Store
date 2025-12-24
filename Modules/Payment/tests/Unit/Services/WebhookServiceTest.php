<?php

namespace Modules\Payment\Tests\Unit\Services;

use Tests\TestCase;
use Modules\Payment\Services\Payment\WebhookService;
use Modules\Order\Contracts\OrderServiceInterface;
use Modules\Payment\Repositories\Interface\Payment\PaymentRepositoryInterface;
use Modules\Payment\Entities\Payment\Payment;
use Stripe\PaymentIntent;
use Mockery;

/**
 * TC-PAY-007: Stripe Webhook - Payment Succeeded
 * TC-PAY-008: Stripe Webhook - Payment Failed
 * TC-PAY-009: Stripe Webhook - Payment Canceled
 */
class WebhookServiceTest extends TestCase
{
    private WebhookService $service;
    private $orderServiceMock;
    private $paymentRepositoryMock;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->orderServiceMock = Mockery::mock(OrderServiceInterface::class);
        $this->paymentRepositoryMock = Mockery::mock(PaymentRepositoryInterface::class);
        $this->service = new WebhookService(
            $this->orderServiceMock,
            $this->paymentRepositoryMock
        );
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_handles_payment_succeeded_when_payment_exists(): void
    {
        // Arrange
        $paymentIntent = Mockery::mock(PaymentIntent::class);
        $paymentIntent->id = 'pi_test123';
        
        $existingPayment = Payment::factory()->make(['id' => 1]);

        $this->paymentRepositoryMock
            ->shouldReceive('findByTransactionId')
            ->with('pi_test123')
            ->once()
            ->andReturn($existingPayment);

        // Act
        $this->service->handlePaymentSucceeded($paymentIntent);

        // Assert - Should not throw exception and should not recreate order
        $this->assertTrue(true);
    }

    public function test_handles_payment_failed(): void
    {
        // Arrange
        $paymentIntent = Mockery::mock(PaymentIntent::class);
        $paymentIntent->id = 'pi_test123';
        $paymentIntent->last_payment_error = (object)['message' => 'Card declined'];

        // Act
        $this->service->handlePaymentFailed($paymentIntent);

        // Assert - Should not throw exception
        $this->assertTrue(true);
    }

    public function test_handles_payment_canceled(): void
    {
        // Arrange
        $paymentIntent = Mockery::mock(PaymentIntent::class);
        $paymentIntent->id = 'pi_test123';

        // Act
        $this->service->handlePaymentCanceled($paymentIntent);

        // Assert - Should not throw exception
        $this->assertTrue(true);
    }
}

