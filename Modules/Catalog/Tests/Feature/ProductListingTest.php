<?php

namespace Modules\Catalog\Tests\Feature;

use Tests\TestCase;
use Modules\Catalog\Entities\Product\Product;
use Modules\Catalog\Entities\Product\ProductTranslation;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * TC-CAT-005: List Products - Public Access
 * TC-CAT-007: Product Multi-Language Support (EN)
 * TC-CAT-008: Product Multi-Language Support (AR)
 */
class ProductListingTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_can_list_products(): void
    {
        // Arrange
        $products = Product::factory()->count(5)->create();

        // Act
        $response = $this->getJson('/api/products');

        // Assert
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'description',
                ],
            ],
        ]);
    }

    public function test_products_returned_with_english_translations(): void
    {
        // Arrange
        $product = Product::factory()->create();
        ProductTranslation::factory()->create([
            'product_id' => $product->id,
            'locale' => 'en',
            'name' => 'English Product Name',
        ]);

        // Act
        $response = $this->withHeader('Accept-Language', 'en')
            ->getJson('/api/products');

        // Assert
        $response->assertStatus(200);
        $response->assertJsonFragment(['name' => 'English Product Name']);
    }

    public function test_products_returned_with_arabic_translations(): void
    {
        // Arrange
        $product = Product::factory()->create();
        ProductTranslation::factory()->create([
            'product_id' => $product->id,
            'locale' => 'ar',
            'name' => 'اسم المنتج',
        ]);

        // Act
        $response = $this->withHeader('Accept-Language', 'ar')
            ->getJson('/api/products');

        // Assert
        $response->assertStatus(200);
        $response->assertJsonFragment(['name' => 'اسم المنتج']);
    }
}

