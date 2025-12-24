<?php

namespace Modules\Order\Tests\Unit\Pipeline;

use Tests\TestCase;
use Modules\Order\Services\Order\Pipeline\ValidateStockStep;
use Modules\Catalog\Entities\Product\Product;
use Modules\Catalog\Contracts\ProductVariantServiceInterface;
use Modules\Offer\Contracts\OfferServiceInterface;
use Modules\Catalog\Exceptions\Product\Variant\OutOfStockException;
use Mockery;

/**
 * TC-ORD-002: Order Creation - Out of Stock
 * TC-ORD-003: Order Creation - Insufficient Stock
 */
class ValidateStockStepTest extends TestCase
{
    private ValidateStockStep $step;
    private $variantServiceMock;
    private $offerServiceMock;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->variantServiceMock = Mockery::mock(ProductVariantServiceInterface::class);
        $this->offerServiceMock = Mockery::mock(OfferServiceInterface::class);
        $this->step = new ValidateStockStep($this->variantServiceMock, $this->offerServiceMock);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_validates_product_stock_successfully(): void
    {
        // Arrange
        $data = [
            'items' => [
                [
                    'orderable_type' => Product::class,
                    'variant_id' => 1,
                    'quantity' => 2,
                ],
            ],
        ];

        $variants = collect([
            1 => (object)['id' => 1, 'quantity' => 10],
        ]);

        $this->variantServiceMock
            ->shouldReceive('getVariantsByIds')
            ->with([1])
            ->once()
            ->andReturn($variants);

        $this->variantServiceMock
            ->shouldReceive('checkStock')
            ->once()
            ->andReturn(true);

        // Act
        $result = $this->step->handle($data, function ($data) {
            return $data;
        });

        // Assert
        $this->assertArrayHasKey('_variants', $result);
        $this->assertArrayHasKey('_grouped_items', $result);
    }

    public function test_throws_exception_when_product_out_of_stock(): void
    {
        // Arrange
        $data = [
            'items' => [
                [
                    'orderable_type' => Product::class,
                    'variant_id' => 1,
                    'quantity' => 1,
                ],
            ],
        ];

        $variants = collect([
            1 => (object)['id' => 1, 'quantity' => 0],
        ]);

        $this->variantServiceMock
            ->shouldReceive('getVariantsByIds')
            ->with([1])
            ->once()
            ->andReturn($variants);

        $this->variantServiceMock
            ->shouldReceive('checkStock')
            ->once()
            ->andThrow(new OutOfStockException('Product is out of stock'));

        // Act & Assert
        $this->expectException(OutOfStockException::class);

        $this->step->handle($data, function ($data) {
            return $data;
        });
    }

    public function test_throws_exception_when_insufficient_stock(): void
    {
        // Arrange
        $data = [
            'items' => [
                [
                    'orderable_type' => Product::class,
                    'variant_id' => 1,
                    'quantity' => 10,
                ],
            ],
        ];

        $variants = collect([
            1 => (object)['id' => 1, 'quantity' => 5],
        ]);

        $this->variantServiceMock
            ->shouldReceive('getVariantsByIds')
            ->with([1])
            ->once()
            ->andReturn($variants);

        $this->variantServiceMock
            ->shouldReceive('checkStock')
            ->once()
            ->andThrow(new OutOfStockException('Insufficient stock'));

        // Act & Assert
        $this->expectException(OutOfStockException::class);

        $this->step->handle($data, function ($data) {
            return $data;
        });
    }
}

