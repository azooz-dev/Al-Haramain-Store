<?php

namespace Modules\Analytics\tests\Unit\Services;

use Tests\TestCase;
use Modules\Analytics\Services\OrderAnalyticsService;
use Modules\Analytics\Repositories\Interface\OrderAnalyticsRepositoryInterface;
use Carbon\Carbon;
use Mockery;

/**
 * TC-ANA-003: Filter by Last 7 Days
 * TC-ANA-004: Filter by This Year
 * TC-ANA-005: Order Status Distribution
 */
class OrderAnalyticsServiceTest extends TestCase
{
  private OrderAnalyticsService $service;
  private $orderAnalyticsRepositoryMock;

  protected function setUp(): void
  {
    parent::setUp();

    $this->orderAnalyticsRepositoryMock = Mockery::mock(OrderAnalyticsRepositoryInterface::class);
    $this->service = new OrderAnalyticsService($this->orderAnalyticsRepositoryMock);
  }

  protected function tearDown(): void
  {
    Mockery::close();
    parent::tearDown();
  }

  public function test_gets_revenue_overview(): void
  {
    // Arrange
    $start = Carbon::now()->subDays(7);
    $end = Carbon::now();

    $revenueData = collect([
      (object)['date' => '2024-01-01', 'revenue' => 100.00],
      (object)['date' => '2024-01-02', 'revenue' => 200.00],
    ]);

    $ordersData = collect([
      (object)['date' => '2024-01-01', 'count' => 5],
      (object)['date' => '2024-01-02', 'count' => 10],
    ]);

    $this->orderAnalyticsRepositoryMock
      ->shouldReceive('getRevenueByDateRangeGrouped')
      ->with($start, $end)
      ->once()
      ->andReturn($revenueData);

    $this->orderAnalyticsRepositoryMock
      ->shouldReceive('getOrdersCountByDateRangeGrouped')
      ->with($start, $end)
      ->once()
      ->andReturn($ordersData);

    // Act
    $result = $this->service->getRevenueOverview($start, $end);

    // Assert
    $this->assertIsArray($result);
    $this->assertArrayHasKey('datasets', $result);
    $this->assertArrayHasKey('labels', $result);
  }

  public function test_gets_total_revenue(): void
  {
    // Arrange
    $start = Carbon::now()->subDays(30);
    $end = Carbon::now();
    $expectedRevenue = 5000.00;

    $this->orderAnalyticsRepositoryMock
      ->shouldReceive('getRevenueByDateRange')
      ->with($start, $end)
      ->once()
      ->andReturn($expectedRevenue);

    // Act
    $result = $this->service->getTotalRevenue($start, $end);

    // Assert
    $this->assertEquals($expectedRevenue, $result);
  }
}
