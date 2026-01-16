<?php

namespace Modules\Catalog\tests\Unit\Services;

use Tests\TestCase;
use Modules\Catalog\Services\Product\ProductStockService;
use Modules\Catalog\Repositories\Interface\Product\ProductRepositoryInterface;
use Mockery;

/**
 * TC-CAT-009: Stock Calculation - Base + Variants
 */
class ProductStockServiceTest extends TestCase
{
    private ProductStockService $service;
    private $productRepositoryMock;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->productRepositoryMock = Mockery::mock(ProductRepositoryInterface::class);
        $this->service = new ProductStockService($this->productRepositoryMock);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_calculates_product_quantities_from_variants(): void
    {
        // Arrange
        $variants = collect([
            1 => (object)['id' => 1, 'product_id' => 10],
            2 => (object)['id' => 2, 'product_id' => 10],
        ]);

        $items = [
            1 => ['quantity' => 5],
            2 => ['quantity' => 3],
        ];

        // Act
        $result = $this->service->calculateProductQuantitiesFromVariants($variants, $items);

        // Assert
        $this->assertEquals(8, $result[10]); // 5 + 3 = 8
    }

    public function test_decrements_product_stock(): void
    {
        // Arrange
        $productQuantities = [
            1 => 5,
            2 => 3,
        ];

        $this->productRepositoryMock
            ->shouldReceive('decrementProductStock')
            ->with(1, 5)
            ->once();

        $this->productRepositoryMock
            ->shouldReceive('decrementProductStock')
            ->with(2, 3)
            ->once();

        // Act
        $this->service->decrementProductStock($productQuantities);

        // Assert - Verified via mock expectations
        $this->assertTrue(true);
    }
}

