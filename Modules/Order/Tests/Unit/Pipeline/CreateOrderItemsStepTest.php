<?php

namespace Modules\Order\Tests\Unit\Pipeline;

use Tests\TestCase;
use Modules\Order\Services\Order\Pipeline\CreateOrderItemsStep;
use Modules\Order\Repositories\Interface\OrderItem\OrderItemRepositoryInterface;
use Modules\Order\Entities\Order\Order;
use Modules\Order\Exceptions\Order\OrderException;
use Mockery;

/**
 * TC-ORD-007: Order Creation - Multiple Items (Products + Offers)
 */
class CreateOrderItemsStepTest extends TestCase
{
    private CreateOrderItemsStep $step;
    private $orderItemRepositoryMock;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->orderItemRepositoryMock = Mockery::mock(OrderItemRepositoryInterface::class);
        $this->step = new CreateOrderItemsStep($this->orderItemRepositoryMock);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_creates_order_items_successfully(): void
    {
        // Arrange
        $order = Order::factory()->make(['id' => 1]);
        $items = [
            ['orderable_type' => 'Product', 'quantity' => 1],
            ['orderable_type' => 'Offer', 'quantity' => 2],
        ];

        $data = [
            '_order' => $order,
            'items' => $items,
        ];

        $this->orderItemRepositoryMock
            ->shouldReceive('createMany')
            ->with($items, 1)
            ->once()
            ->andReturn(true);

        // Act
        $result = $this->step->handle($data, function ($data) {
            return $data;
        });

        // Assert
        $this->assertIsArray($result);
    }

    public function test_throws_exception_when_order_missing(): void
    {
        // Arrange
        $data = [
            'items' => [],
        ];

        // Act & Assert
        $this->expectException(OrderException::class);
        $this->expectExceptionCode(500);

        $this->step->handle($data, function ($data) {
            return $data;
        });
    }

    public function test_throws_exception_when_items_creation_fails(): void
    {
        // Arrange
        $order = Order::factory()->make(['id' => 1]);
        $items = [];

        $data = [
            '_order' => $order,
            'items' => $items,
        ];

        $this->orderItemRepositoryMock
            ->shouldReceive('createMany')
            ->with($items, 1)
            ->once()
            ->andReturn(false);

        // Act & Assert
        $this->expectException(OrderException::class);
        $this->expectExceptionCode(500);

        $this->step->handle($data, function ($data) {
            return $data;
        });
    }
}

