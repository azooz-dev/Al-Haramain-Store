<?php

namespace Modules\Order\Tests\Unit\Pipeline;

use Tests\TestCase;
use Modules\Order\Services\Order\Pipeline\ProcessPaymentStep;
use Modules\Payment\Contracts\PaymentServiceInterface;
use Mockery;

class ProcessPaymentStepTest extends TestCase
{
    private ProcessPaymentStep $step;
    private $paymentServiceMock;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->paymentServiceMock = Mockery::mock(PaymentServiceInterface::class);
        $this->step = new ProcessPaymentStep($this->paymentServiceMock);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_processes_payment_successfully(): void
    {
        // Arrange
        $paymentResult = Mockery::mock(\Modules\Payment\DTOs\PaymentResult::class);
        $paymentResult->transactionId = 'txn_123';
        $paymentResult->success = true;
        
        $data = [
            'payment_method' => 'credit_card',
            'total_amount' => 100.00,
        ];

        $this->paymentServiceMock
            ->shouldReceive('processPayment')
            ->with($data)
            ->once()
            ->andReturn($paymentResult);

        // Act
        $result = $this->step->handle($data, function ($data) {
            return $data;
        });

        // Assert
        $this->assertArrayHasKey('_payment_result', $result);
        $this->assertEquals($paymentResult, $result['_payment_result']);
    }
}

