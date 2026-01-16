<?php

namespace Modules\Analytics\tests\Unit\Services;

use Tests\TestCase;
use Modules\Analytics\Services\CustomerAnalyticsService;
use Modules\Analytics\Repositories\Interface\UserAnalyticsRepositoryInterface;
use Modules\Analytics\Repositories\Interface\CategoryAnalyticsRepositoryInterface;
use Modules\Catalog\Contracts\CategoryTranslationServiceInterface;
use Carbon\Carbon;
use Mockery;

class CustomerAnalyticsServiceTest extends TestCase
{
    private CustomerAnalyticsService $service;
    private $userAnalyticsRepositoryMock;
    private $categoryAnalyticsRepositoryMock;
    private $categoryTranslationServiceMock;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->userAnalyticsRepositoryMock = Mockery::mock(UserAnalyticsRepositoryInterface::class);
        $this->categoryAnalyticsRepositoryMock = Mockery::mock(CategoryAnalyticsRepositoryInterface::class);
        $this->categoryTranslationServiceMock = Mockery::mock(CategoryTranslationServiceInterface::class);
        
        $this->service = new CustomerAnalyticsService(
            $this->userAnalyticsRepositoryMock,
            $this->categoryAnalyticsRepositoryMock,
            $this->categoryTranslationServiceMock
        );
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

        $this->userAnalyticsRepositoryMock
            ->shouldReceive('getUsersCountByDateRange')
            ->with($start, $end)
            ->once()
            ->andReturn($expectedCount);

        // Act
        $result = $this->service->getNewCustomers($start, $end);

        // Assert
        $this->assertEquals($expectedCount, $result);
    }
}

