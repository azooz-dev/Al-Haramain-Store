<?php

namespace Tests\Feature\Product;

use Tests\TestCase;
use Tests\Fixtures\OrderFixtures;
use App\Services\Product\Variant\ProductVariantService;
use App\Exceptions\Product\Variant\OutOfStockException;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * Product Variant Service Tests
 */
class ProductVariantServiceTest extends TestCase
{
    use RefreshDatabase;

    private ProductVariantService $variantService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->variantService = app(ProductVariantService::class);
    }

    /**
     * Test stock validation succeeds with sufficient stock
     */
    public function test_stock_validation_succeeds_with_sufficient_stock(): void
    {
        // Arrange
        $productData = OrderFixtures::createProductWithVariantInStock(100, 100.00);

        $items = [
            $productData['variant']->id => [
                'quantity' => 50,
                'variant_id' => $productData['variant']->id,
            ],
        ];

        // Act & Assert - Should not throw exception
        $this->variantService->checkStock($items);
        $this->assertTrue(true);
    }

    /**
     * Test stock validation fails with insufficient stock
     */
    public function test_stock_validation_fails_with_insufficient_stock(): void
    {
        // Arrange
        $productData = OrderFixtures::createProductWithVariantInStock(5, 100.00);

        $items = [
            $productData['variant']->id => [
                'quantity' => 10, // More than available
                'variant_id' => $productData['variant']->id,
            ],
        ];

        // Act & Assert
        $this->expectException(OutOfStockException::class);
        $this->variantService->checkStock($items);
    }

    /**
     * Test stock decrement works correctly
     */
    public function test_stock_decrement_works_correctly(): void
    {
        // Arrange
        $productData = OrderFixtures::createProductWithVariantInStock(100, 100.00);

        $initialQuantity = $productData['variant']->quantity;
        $items = [
            $productData['variant']->id => [
                'quantity' => 25,
                'variant_id' => $productData['variant']->id,
            ],
        ];

        // Act
        $this->variantService->decrementVariantStock($items);

        // Assert
        $productData['variant']->refresh();
        $this->assertEquals($initialQuantity - 25, $productData['variant']->quantity);
    }

    /**
     * Test total order price calculation
     */
    public function test_total_order_price_calculation(): void
    {
        // Arrange
        $product1 = \App\Models\Product\Product::factory()->create();
        $color1 = \App\Models\Product\ProductColor::factory()->create(['product_id' => $product1->id]);
        $variant1 = \App\Models\Product\ProductVariant::factory()->create([
            'product_id' => $product1->id,
            'color_id' => $color1->id,
            'quantity' => 100,
            'price' => 100.00,
            'amount_discount_price' => null // Explicitly no discount
        ]);

        $product2 = \App\Models\Product\Product::factory()->create();
        $color2 = \App\Models\Product\ProductColor::factory()->create(['product_id' => $product2->id]);
        $variant2 = \App\Models\Product\ProductVariant::factory()->create([
            'product_id' => $product2->id,
            'color_id' => $color2->id,
            'quantity' => 50,
            'price' => 150.00,
            'amount_discount_price' => null // Explicitly no discount
        ]);

        $items = [
            [
                'orderable_type' => \App\Models\Product\Product::class,
                'variant_id' => $variant1->id,
                'quantity' => 2,
            ],
            [
                'orderable_type' => \App\Models\Product\Product::class,
                'variant_id' => $variant2->id,
                'quantity' => 3,
            ],
        ];

        // Act
        $total = $this->variantService->calculateTotalOrderPrice($items);

        // Assert
        $expectedTotal = (100.00 * 2) + (150.00 * 3); // 200 + 450 = 650
        $this->assertEquals($expectedTotal, $total);
    }
}
