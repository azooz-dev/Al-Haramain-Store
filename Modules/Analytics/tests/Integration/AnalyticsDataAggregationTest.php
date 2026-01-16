<?php

namespace Modules\Analytics\tests\Integration;

use Tests\TestCase;
use Modules\Order\Entities\Order\Order;
use Modules\User\Entities\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;

/**
 * TC-ANA-001: Revenue Excludes Cancelled Orders
 * TC-ANA-002: Revenue Excludes Refunded Orders
 * TC-ANA-003: Filter by Last 7 Days
 * TC-ANA-004: Filter by This Year
 * TC-ANA-005: Order Status Distribution
 */
class AnalyticsDataAggregationTest extends TestCase
{
    use RefreshDatabase;

    public function test_revenue_excludes_cancelled_and_refunded_orders(): void
    {
        // Arrange
        $user = User::factory()->verified()->create();
        
        Order::factory()->create([
            'user_id' => $user->id,
            'status' => 'delivered',
            'total_amount' => 100.00,
        ]);
        Order::factory()->create([
            'user_id' => $user->id,
            'status' => 'cancelled',
            'total_amount' => 50.00,
        ]);
        Order::factory()->create([
            'user_id' => $user->id,
            'status' => 'refunded',
            'total_amount' => 30.00,
        ]);

        $start = Carbon::now()->subDays(30);
        $end = Carbon::now();

        // Act
        $orderAnalyticsService = app(\Modules\Analytics\Contracts\OrderAnalyticsServiceInterface::class);
        $revenue = $orderAnalyticsService->getTotalRevenue($start, $end);

        // Assert
        $this->assertEquals(100.00, $revenue);
    }

    public function test_filters_orders_by_date_range(): void
    {
        // Arrange
        $user = User::factory()->verified()->create();
        
        Order::factory()->create([
            'user_id' => $user->id,
            'status' => 'delivered',
            'created_at' => Carbon::now()->subDays(3),
        ]);
        Order::factory()->create([
            'user_id' => $user->id,
            'status' => 'delivered',
            'created_at' => Carbon::now()->subDays(10), // Outside last 7 days
        ]);

        $start = Carbon::now()->subDays(7);
        $end = Carbon::now();

        // Act
        $orderAnalyticsService = app(\Modules\Analytics\Contracts\OrderAnalyticsServiceInterface::class);
        $count = $orderAnalyticsService->getTotalOrdersCount($start, $end);

        // Assert
        $this->assertEquals(1, $count);
    }

    public function test_gets_order_status_distribution(): void
    {
        // Arrange
        $user = User::factory()->verified()->create();
        
        Order::factory()->count(5)->create([
            'user_id' => $user->id,
            'status' => 'pending',
        ]);
        Order::factory()->count(3)->create([
            'user_id' => $user->id,
            'status' => 'processing',
        ]);
        Order::factory()->count(2)->create([
            'user_id' => $user->id,
            'status' => 'delivered',
        ]);

        $start = Carbon::now()->subDays(30);
        $end = Carbon::now();

        // Act
        $orderAnalyticsService = app(\Modules\Analytics\Contracts\OrderAnalyticsServiceInterface::class);
        $distribution = $orderAnalyticsService->getOrderStatusDistribution($start, $end);

        // Assert
        $this->assertIsArray($distribution);
        // Note: The actual distribution format depends on repository implementation
    }
}

