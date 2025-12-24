<?php

namespace Modules\Catalog\Tests\Feature;

use Tests\TestCase;
use Modules\Catalog\Entities\Product\Product;
use Modules\Catalog\Entities\Product\ProductColor;
use Modules\Catalog\Entities\Product\ProductVariant;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * TC-CAT-006: Product Details with Variants
 */
class ProductDetailsTest extends TestCase
{
    use RefreshDatabase;

    public function test_returns_product_details_with_variants(): void
    {
        // Arrange
        $product = Product::factory()->create();
        $color = ProductColor::factory()->create(['product_id' => $product->id]);
        $variant = ProductVariant::factory()->create([
            'product_id' => $product->id,
            'color_id' => $color->id,
        ]);

        // Act
        $response = $this->getJson("/api/products/{$product->id}");

        // Assert
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'colors',
                'variants',
            ],
        ]);
    }
}

