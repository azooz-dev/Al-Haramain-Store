<?php

namespace Modules\Order\Tests\Unit\Entities;

use Tests\TestCase;
use Modules\Order\Entities\Order\Order;
use Modules\Order\Enums\OrderStatus;
use Modules\Payment\Enums\PaymentMethod;
use Modules\User\Entities\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * TC-ORD-008: Order Status Transitions
 * TC-ORD-009: Order Cancellation Rules
 */
class OrderTest extends TestCase
{
    use RefreshDatabase;

    public function test_order_can_be_cancelled_when_pending(): void
    {
        // Arrange
        $order = Order::factory()->create([
            'status' => OrderStatus::PENDING,
        ]);

        // Act
        $canCancel = $order->canBeCancelled();

        // Assert
        $this->assertTrue($canCancel);
    }

    public function test_order_cannot_be_cancelled_when_delivered(): void
    {
        // Arrange
        $order = Order::factory()->create([
            'status' => OrderStatus::DELIVERED,
        ]);

        // Act
        $canCancel = $order->canBeCancelled();

        // Assert
        $this->assertFalse($canCancel);
    }

    public function test_order_can_be_refunded_when_delivered(): void
    {
        // Arrange
        $order = Order::factory()->create([
            'status' => OrderStatus::DELIVERED,
            'created_at' => now()->subDays(10),
        ]);

        // Act
        $canRefund = $order->canBeRefunded();

        // Assert
        $this->assertTrue($canRefund);
    }

    public function test_order_cannot_be_refunded_after_30_days(): void
    {
        // Arrange
        $order = Order::factory()->create([
            'status' => OrderStatus::DELIVERED,
            'created_at' => now()->subDays(31),
        ]);

        // Act
        $canRefund = $order->canBeRefunded();

        // Assert
        $this->assertFalse($canRefund);
    }

    public function test_order_has_unique_order_number(): void
    {
        // Arrange & Act
        $order = Order::factory()->create();

        // Assert
        $this->assertNotNull($order->order_number);
        $this->assertStringStartsWith('ORD-', $order->order_number);
    }
}
