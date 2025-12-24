<?php

namespace Modules\Analytics\Tests\Unit\Services;

use Tests\TestCase;
use Modules\Analytics\Services\CustomerAnalyticsService;
use Modules\Analytics\Repositories\Interface\CustomerAnalyticsRepositoryInterface;
use Carbon\Carbon;
use Mockery;

class CustomerAnalyticsServiceTest extends TestCase
{
    private CustomerAnalyticsService $service;
    private $customerAnalyticsRepositoryMock;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->customerAnalyticsRepositoryMock = Mockery::mock(CustomerAnalyticsRepositoryInterface::class);
        $this->service = new CustomerAnalyticsService($this->customerAnalyticsRepositoryMock);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_gets_new_customers_count(): void
    {
        // Arrange
        $start = Carbon::now()->subDays(30);
        $end = Carbon::now();
        $expectedCount = 10;

        $this->customerAnalyticsRepositoryMock
            ->shouldReceive('getNewCustomers')
            ->with($start, $end)
            ->once()
            ->andReturn($expectedCount);

        // Act
        $result = $this->service->getNewCustomers($start, $end);

        // Assert
        $this->assertEquals($expectedCount, $result);
    }
}

