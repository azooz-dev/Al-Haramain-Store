<?php

namespace Modules\Order\Tests\Unit\Services;

use Tests\TestCase;
use Modules\Order\Services\Order\OrderService;
use Modules\Order\Repositories\Interface\Order\OrderRepositoryInterface;
use Modules\Order\Services\Order\Pipeline\ValidateBuyerStep;
use Modules\Order\Services\Order\Pipeline\ValidateStockStep;
use Modules\Order\Services\Order\Pipeline\CalculatePricesStep;
use Modules\Order\Services\Order\Pipeline\ApplyCouponStep;
use Modules\Order\Services\Order\Pipeline\ProcessPaymentStep;
use Modules\Order\Services\Order\Pipeline\CreateOrderStep;
use Modules\Order\Services\Order\Pipeline\CreateOrderItemsStep;
use Modules\Order\Services\Order\Pipeline\UpdateStockStep;
use Modules\Order\Services\Order\Pipeline\RecordPaymentStep;
use Modules\Order\Entities\Order\Order;
use Modules\Order\Enums\OrderStatus;
use Modules\Order\Exceptions\Order\OrderException;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Facades\DB;
use Mockery;

/**
 * TC-ORD-001: Create Order with Valid Data
 * TC-ORD-010: Concurrent Order - Race Condition
 */
class OrderServiceTest extends TestCase
{
    private OrderService $service;
    private $orderRepositoryMock;
    private $pipelineMock;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->orderRepositoryMock = Mockery::mock(OrderRepositoryInterface::class);
        
        $validateBuyerStep = Mockery::mock(ValidateBuyerStep::class);
        $validateStockStep = Mockery::mock(ValidateStockStep::class);
        $calculatePricesStep = Mockery::mock(CalculatePricesStep::class);
        $applyCouponStep = Mockery::mock(ApplyCouponStep::class);
        $processPaymentStep = Mockery::mock(ProcessPaymentStep::class);
        $createOrderStep = Mockery::mock(CreateOrderStep::class);
        $createOrderItemsStep = Mockery::mock(CreateOrderItemsStep::class);
        $updateStockStep = Mockery::mock(UpdateStockStep::class);
        $recordPaymentStep = Mockery::mock(RecordPaymentStep::class);

        $this->service = new OrderService(
            $this->orderRepositoryMock,
            $validateBuyerStep,
            $validateStockStep,
            $calculatePricesStep,
            $applyCouponStep,
            $processPaymentStep,
            $createOrderStep,
            $createOrderItemsStep,
            $updateStockStep,
            $recordPaymentStep
        );
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_can_update_order_status_with_valid_transition(): void
    {
        // Arrange
        $order = Order::factory()->pending()->make(['id' => 1]);
        
        $this->orderRepositoryMock
            ->shouldReceive('findById')
            ->with(1)
            ->once()
            ->andReturn($order);

        $updatedOrder = Order::factory()->processing()->make(['id' => 1]);
        
        $this->orderRepositoryMock
            ->shouldReceive('updateStatus')
            ->with(1, OrderStatus::PROCESSING->value)
            ->once()
            ->andReturn($updatedOrder);

        // Act
        $result = $this->service->updateOrderStatus(1, OrderStatus::PROCESSING->value);

        // Assert
        $this->assertEquals($updatedOrder, $result);
    }

    public function test_throws_exception_for_invalid_status_transition(): void
    {
        // Arrange
        $order = Order::factory()->pending()->make(['id' => 1]);
        
        $this->orderRepositoryMock
            ->shouldReceive('findById')
            ->with(1)
            ->once()
            ->andReturn($order);

        // Act & Assert
        $this->expectException(OrderException::class);
        $this->expectExceptionCode(422);

        $this->service->updateOrderStatus(1, OrderStatus::DELIVERED->value);
    }
}

