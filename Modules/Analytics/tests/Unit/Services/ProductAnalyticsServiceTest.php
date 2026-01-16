<?php

namespace Modules\Analytics\tests\Unit\Services;

use Tests\TestCase;
use Modules\Analytics\Services\ProductAnalyticsService;
use Modules\Analytics\Repositories\Interface\ProductAnalyticsRepositoryInterface;
use Modules\Catalog\Contracts\ProductTranslationServiceInterface;
use Carbon\Carbon;
use Mockery;

class ProductAnalyticsServiceTest extends TestCase
{
    private ProductAnalyticsService $service;
    private $productAnalyticsRepositoryMock;
    private $productTranslationServiceMock;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->productAnalyticsRepositoryMock = Mockery::mock(ProductAnalyticsRepositoryInterface::class);
        $this->productTranslationServiceMock = Mockery::mock(ProductTranslationServiceInterface::class);
        
        $this->service = new ProductAnalyticsService(
            $this->productAnalyticsRepositoryMock,
            $this->productTranslationServiceMock
        );
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_gets_top_selling_products(): void
    {
        // Arrange
        $start = Carbon::now()->subDays(30);
        $end = Carbon::now();
        $limit = 3;
        $products = collect([]);

        $this->productAnalyticsRepositoryMock
            ->shouldReceive('getTopSellingProducts')
            ->with($start, $end, $limit)
            ->once()
            ->andReturn($products);

        // Act
        $result = $this->service->getTopSellingProducts($start, $end, $limit);

        // Assert
        $this->assertInstanceOf(\Illuminate\Support\Collection::class, $result);
    }
}

