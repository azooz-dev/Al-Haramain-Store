<?php

namespace Modules\Payment\Tests\Unit\Services;

use Tests\TestCase;
use Modules\Payment\Services\Payment\WebhookService;
use Modules\Order\Contracts\OrderServiceInterface;
use Modules\Payment\Repositories\Interface\Payment\PaymentRepositoryInterface;
use Modules\Payment\Entities\Payment\Payment;
use Stripe\PaymentIntent;
use Mockery;

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
        // Arrange - Create a simple test double for PaymentIntent
        // Since Stripe objects don't allow direct property setting, we create a minimal object
        $paymentIntent = new class extends PaymentIntent {
            public $id = 'pi_test123';
            public $metadata = [];

            public function __construct()
            {
                // Skip parent constructor to avoid Stripe API calls
            }
        };

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
        // Arrange - Create a simple test double for PaymentIntent
        $paymentIntent = new class extends PaymentIntent {
            public $id = 'pi_test123';
            public $last_payment_error = null;

            public function __construct()
            {
                // Skip parent constructor to avoid Stripe API calls
            }
        };
        $paymentIntent->last_payment_error = (object)['message' => 'Card declined'];

        // Act
        $this->service->handlePaymentFailed($paymentIntent);

        // Assert - Should not throw exception
        $this->assertTrue(true);
    }

    public function test_handles_payment_canceled(): void
    {
        // Arrange - Create a simple test double for PaymentIntent
        $paymentIntent = new class extends PaymentIntent {
            public $id = 'pi_test123';

            public function __construct()
            {
                // Skip parent constructor to avoid Stripe API calls
            }
        };

        // Act
        $this->service->handlePaymentCanceled($paymentIntent);

        // Assert - Should not throw exception
        $this->assertTrue(true);
    }
}
