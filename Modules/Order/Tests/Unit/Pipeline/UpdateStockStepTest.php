<?php

namespace Modules\Order\Tests\Unit\Pipeline;

use Tests\TestCase;
use Modules\Order\Services\Order\Pipeline\UpdateStockStep;
use Modules\Catalog\Contracts\ProductVariantServiceInterface;
use Modules\Catalog\Contracts\ProductStockServiceInterface;
use Modules\Catalog\Entities\Product\Product;
use Mockery;

/**
 * TC-ORD-009: Stock Deduction After Order
 */
class UpdateStockStepTest extends TestCase
{
    private UpdateStockStep $step;
    private $variantServiceMock;
    private $productStockServiceMock;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->variantServiceMock = Mockery::mock(ProductVariantServiceInterface::class);
        $this->productStockServiceMock = Mockery::mock(ProductStockServiceInterface::class);
        $this->step = new UpdateStockStep($this->variantServiceMock, $this->productStockServiceMock);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_decrements_variant_stock_successfully(): void
    {
        // Arrange
        $groupedItems = [
            Product::class => [
                1 => [
                    'quantity' => 3,
                    'variant_id' => 1,
                    'color_id' => 1,
                ],
            ],
        ];

        $variants = collect([
            1 => (object)['id' => 1],
        ]);

        $data = [
            '_grouped_items' => $groupedItems,
            '_offers' => collect(),
        ];

        $this->variantServiceMock
            ->shouldReceive('getVariantsByIds')
            ->with([1])
            ->once()
            ->andReturn($variants);

        $this->variantServiceMock
            ->shouldReceive('decrementVariantStock')
            ->with($groupedItems[Product::class])
            ->once();

        $this->productStockServiceMock
            ->shouldReceive('calculateProductQuantitiesFromVariants')
            ->with($variants, $groupedItems[Product::class])
            ->once()
            ->andReturn([]);

        $this->productStockServiceMock
            ->shouldReceive('decrementProductStock')
            ->with([])
            ->once();

        // Act
        $result = $this->step->handle($data, function ($data) {
            return $data;
        });

        // Assert
        $this->assertIsArray($result);
    }

    public function test_skips_stock_update_when_no_items(): void
    {
        // Arrange
        $data = [
            '_grouped_items' => [],
            '_offers' => collect(),
        ];

        // Act
        $result = $this->step->handle($data, function ($data) {
            return $data;
        });

        // Assert
        $this->assertIsArray($result);
    }
}

