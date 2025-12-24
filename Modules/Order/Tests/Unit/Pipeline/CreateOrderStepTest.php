<?php

namespace Modules\Order\Tests\Unit\Pipeline;

use Tests\TestCase;
use Modules\Order\Services\Order\Pipeline\CreateOrderStep;
use Modules\Order\Repositories\Interface\Order\OrderRepositoryInterface;
use Modules\Order\Entities\Order\Order;
use Modules\Order\Exceptions\Order\OrderException;
use Mockery;

class CreateOrderStepTest extends TestCase
{
    private CreateOrderStep $step;
    private $orderRepositoryMock;

    protected function setUp(): void
    {
        parent::setUp();
        $this->orderRepositoryMock = Mockery::mock(OrderRepositoryInterface::class);
        $this->step = new CreateOrderStep($this->orderRepositoryMock);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_creates_order_successfully(): void
    {
        $data = ['user_id' => 1, 'total_amount' => 100.00];
        $order = Order::factory()->make(['id' => 1]);
        $this->orderRepositoryMock->shouldReceive('store')->with($data)->once()->andReturn($order);
        $result = $this->step->handle($data, function ($data) {
            return $data;
        });
        $this->assertArrayHasKey('_order', $result);
    }

    public function test_throws_exception_when_order_creation_fails(): void
    {
        $data = ['user_id' => 1];
        $this->orderRepositoryMock
            ->shouldReceive('store')
            ->with($data)
            ->once()
            ->andThrow(new OrderException('Order creation failed', 500));
        $this->expectException(OrderException::class);
        $this->step->handle($data, function ($data) {
            return $data;
        });
    }
}
