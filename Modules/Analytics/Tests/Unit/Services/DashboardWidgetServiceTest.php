<?php

namespace Modules\Analytics\Tests\Unit\Services;

use Tests\TestCase;
use Modules\Analytics\Services\DashboardWidgetService;
use Modules\Analytics\Repositories\Interface\OrderAnalyticsRepositoryInterface;
use Modules\Analytics\Repositories\Interface\UserAnalyticsRepositoryInterface;
use Modules\Analytics\Repositories\Interface\ProductAnalyticsRepositoryInterface;
use Mockery;

/**
 * TC-ANA-001: Revenue Excludes Cancelled Orders
 * TC-ANA-002: Revenue Excludes Refunded Orders
 */
class DashboardWidgetServiceTest extends TestCase
{
    private DashboardWidgetService $service;
    private $orderAnalyticsRepositoryMock;
    private $userAnalyticsRepositoryMock;
    private $productAnalyticsRepositoryMock;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->orderAnalyticsRepositoryMock = Mockery::mock(OrderAnalyticsRepositoryInterface::class);
        $this->userAnalyticsRepositoryMock = Mockery::mock(UserAnalyticsRepositoryInterface::class);
        $this->productAnalyticsRepositoryMock = Mockery::mock(ProductAnalyticsRepositoryInterface::class);
        
        $this->service = new DashboardWidgetService(
            $this->orderAnalyticsRepositoryMock,
            $this->userAnalyticsRepositoryMock,
            $this->productAnalyticsRepositoryMock
        );
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_gets_todays_revenue(): void
    {
        // Arrange
        $expectedRevenue = 100.00; // Only DELIVERED orders

        $this->orderAnalyticsRepositoryMock
            ->shouldReceive('getRevenueByDateRange')
            ->once()
            ->andReturn($expectedRevenue);

        // Act
        $result = $this->service->getTodaysRevenue();

        // Assert
        $this->assertEquals($expectedRevenue, $result);
    }

    public function test_gets_total_orders_today(): void
    {
        // Arrange
        $expectedCount = 5;

        $this->orderAnalyticsRepositoryMock
            ->shouldReceive('getOrdersCountByDateRange')
            ->once()
            ->andReturn($expectedCount);

        // Act
        $result = $this->service->getTotalOrdersToday();

        // Assert
        $this->assertEquals($expectedCount, $result);
    }
}
