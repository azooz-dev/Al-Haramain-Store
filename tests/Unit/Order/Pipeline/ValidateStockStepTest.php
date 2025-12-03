<?php

namespace Tests\Unit\Order\Pipeline;

use Tests\TestCase;
use Modules\Catalog\Entities\Product\Product;
use Modules\Catalog\Entities\Product\ProductColor;
use Modules\Catalog\Entities\Product\ProductVariant;
use App\Services\Order\Pipeline\ValidateStockStep;
use App\Exceptions\Product\Variant\OutOfStockException;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * Unit tests for ValidateStockStep
 */
class ValidateStockStepTest extends TestCase
{
    use RefreshDatabase;

    private ValidateStockStep $step;

    protected function setUp(): void
    {
        parent::setUp();
        $this->step = app(ValidateStockStep::class);
    }

    /**
     * Test stock validation succeeds with sufficient stock
     */
    public function test_stock_validation_succeeds_with_sufficient_stock(): void
    {
        // Arrange
        $product = Product::factory()->create();
        $color = ProductColor::factory()->create(['product_id' => $product->id]);
        $variant = ProductVariant::factory()->create([
            'product_id' => $product->id,
            'color_id' => $color->id,
            'quantity' => 100,
            'price' => 100.00,
        ]);

        $data = [
            'items' => [
                [
                    'orderable_type' => Product::class,
                    'variant_id' => $variant->id,
                    'quantity' => 10,
                ],
            ],
        ];

        // Act & Assert - Should not throw exception
        $result = $this->step->handle($data, fn($data) => $data);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('_variants', $result);
    }

    /**
     * Test stock validation fails with insufficient stock
     */
    public function test_stock_validation_fails_with_insufficient_stock(): void
    {
        // Arrange
        $product = Product::factory()->create();
        $color = ProductColor::factory()->create(['product_id' => $product->id]);
        $variant = ProductVariant::factory()->create([
            'product_id' => $product->id,
            'color_id' => $color->id,
            'quantity' => 5,
            'price' => 100.00,
        ]);

        $data = [
            'items' => [
                [
                    'orderable_type' => Product::class,
                    'variant_id' => $variant->id,
                    'quantity' => 10, // More than available
                ],
            ],
        ];

        // Act & Assert
        $this->expectException(OutOfStockException::class);

        $this->step->handle($data, fn($data) => $data);
    }

    /**
     * Test stock validation handles multiple variants
     */
    public function test_stock_validation_handles_multiple_variants(): void
    {
        // Arrange
        $product = Product::factory()->create();
        $color = ProductColor::factory()->create(['product_id' => $product->id]);

        $variant1 = ProductVariant::factory()->create([
            'product_id' => $product->id,
            'color_id' => $color->id,
            'quantity' => 50,
            'price' => 100.00,
        ]);

        $variant2 = ProductVariant::factory()->create([
            'product_id' => $product->id,
            'color_id' => $color->id,
            'quantity' => 30,
            'price' => 150.00,
        ]);

        $data = [
            'items' => [
                [
                    'orderable_type' => Product::class,
                    'variant_id' => $variant1->id,
                    'quantity' => 10,
                ],
                [
                    'orderable_type' => Product::class,
                    'variant_id' => $variant2->id,
                    'quantity' => 5,
                ],
            ],
        ];

        // Act & Assert - Should not throw exception
        $result = $this->step->handle($data, fn($data) => $data);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('_variants', $result);
    }
}
