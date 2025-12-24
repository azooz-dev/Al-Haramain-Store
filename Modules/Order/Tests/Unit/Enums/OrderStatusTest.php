<?php

namespace Modules\Order\Tests\Unit\Enums;

use Tests\TestCase;
use Modules\Order\Enums\OrderStatus;

/**
 * TC-ORD-008: Order Status Transitions
 */
class OrderStatusTest extends TestCase
{
  public function test_pending_can_transition_to_processing(): void
  {
    // Act
    $canTransition = OrderStatus::PENDING->canTransitionTo(OrderStatus::PROCESSING);

    // Assert
    $this->assertTrue($canTransition);
  }

  public function test_pending_cannot_transition_to_delivered(): void
  {
    // Act
    $canTransition = OrderStatus::PENDING->canTransitionTo(OrderStatus::DELIVERED);

    // Assert
    $this->assertFalse($canTransition);
  }

  public function test_processing_can_be_cancelled(): void
  {
    // Act
    $canCancel = OrderStatus::PROCESSING->canBeCancelled();

    // Assert
    $this->assertTrue($canCancel);
  }

  public function test_delivered_can_be_refunded(): void
  {
    // Act
    $canRefund = OrderStatus::DELIVERED->canBeRefunded();

    // Assert
    $this->assertTrue($canRefund);
  }

  public function test_cancelled_cannot_transition(): void
  {
    // Act
    $allowedTransitions = OrderStatus::CANCELLED->allowedTransitions();

    // Assert
    $this->assertEmpty($allowedTransitions);
  }

  public function test_excluded_from_stats_includes_cancelled_and_refunded(): void
  {
    // Act
    $excluded = OrderStatus::excludedFromStats();

    // Assert
    $this->assertContains(OrderStatus::CANCELLED, $excluded);
    $this->assertContains(OrderStatus::REFUNDED, $excluded);
    $this->assertNotContains(OrderStatus::DELIVERED, $excluded);
  }
}
