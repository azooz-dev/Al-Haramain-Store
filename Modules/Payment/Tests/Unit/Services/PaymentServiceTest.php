<?php

namespace Modules\Payment\Tests\Unit\Services;

use Tests\TestCase;
use Modules\Payment\Services\Payment\PaymentService;
use Modules\Payment\Repositories\Interface\Payment\PaymentRepositoryInterface;
use Modules\Payment\Services\Payment\PaymentProcessorFactory;
use Modules\Payment\DTOs\PaymentResult;
use Modules\Payment\Entities\Payment\Payment;
use Carbon\Carbon;
use Mockery;

/**
 * TC-PAY-004: Payment Service - Create Payment Intent
 * TC-PAY-005: Payment Service - Process Payment
 * TC-PAY-006: Payment Service - Create Payment Record
 */
class PaymentServiceTest extends TestCase
{
    private PaymentService $service;
    private $paymentRepositoryMock;
    private $processorFactoryMock;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->paymentRepositoryMock = Mockery::mock(PaymentRepositoryInterface::class);
        $this->processorFactoryMock = Mockery::mock(PaymentProcessorFactory::class);
        $this->service = new PaymentService(
            $this->paymentRepositoryMock,
            $this->processorFactoryMock
        );
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_creates_payment_record_successfully(): void
    {
        // Arrange
        $orderId = 1;
        $paymentResult = new PaymentResult(
            true,
            'txn_123',
            'credit_card',
            100.00,
            Carbon::now()
        );

        $payment = Payment::factory()->make(['id' => 1]);

        $this->paymentRepositoryMock
            ->shouldReceive('create')
            ->with($orderId, $paymentResult)
            ->once()
            ->andReturn($payment);

        // Act
        $result = $this->service->createPayment($orderId, $paymentResult);

        // Assert
        $this->assertInstanceOf(Payment::class, $result);
    }
}
