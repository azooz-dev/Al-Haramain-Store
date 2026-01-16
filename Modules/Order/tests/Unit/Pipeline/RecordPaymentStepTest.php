<?php

namespace Modules\Order\tests\Unit\Pipeline;

use Tests\TestCase;
use Modules\Order\Services\Order\Pipeline\RecordPaymentStep;
use Modules\Payment\Contracts\PaymentServiceInterface;
use Modules\Order\Entities\Order\Order;
use Mockery;

class RecordPaymentStepTest extends TestCase
{
    private RecordPaymentStep $step;
    private $paymentServiceMock;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->paymentServiceMock = Mockery::mock(PaymentServiceInterface::class);
        $this->step = new RecordPaymentStep($this->paymentServiceMock);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_records_payment_for_credit_card(): void
    {
        // Arrange
        $order = Order::factory()->make(['id' => 1]);
        $paymentResult = Mockery::mock(\Modules\Payment\DTOs\PaymentResult::class);
        $paymentResult->transactionId = 'txn_123';
        $paymentResult->success = true;

        $data = [
            '_order' => $order,
            '_payment_result' => $paymentResult,
            'payment_method' => 'credit_card',
        ];

        $this->paymentServiceMock
            ->shouldReceive('createPayment')
            ->with(1, $paymentResult)
            ->once();

        // Act
        $result = $this->step->handle($data, function ($data) {
            return $data;
        });

        // Assert
        $this->assertIsArray($result);
    }

    public function test_skips_payment_recording_for_cod(): void
    {
        // Arrange
        $order = Order::factory()->make(['id' => 1]);
        $data = [
            '_order' => $order,
            'payment_method' => 'cash_on_delivery',
        ];

        // Act
        $result = $this->step->handle($data, function ($data) {
            return $data;
        });

        // Assert
        $this->assertIsArray($result);
    }
}

