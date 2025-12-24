<?php

namespace Modules\Favorite\Tests\Feature;

use Tests\TestCase;
use Modules\User\Entities\User;
use Modules\Catalog\Entities\Product\Product;
use Modules\Catalog\Entities\Product\ProductColor;
use Modules\Catalog\Entities\Product\ProductVariant;
use Modules\Favorite\Entities\Favorite\Favorite;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * TC-FAV-001: Add to Favorites
 */
class AddFavoriteTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_add_product_to_favorites(): void
    {
        // Arrange
        $user = User::factory()->verified()->create();
        $product = Product::factory()->create();
        $color = ProductColor::factory()->create(['product_id' => $product->id]);
        $variant = ProductVariant::factory()->create([
            'product_id' => $product->id,
            'color_id' => $color->id,
        ]);

        // Act
        $response = $this->actingAs($user, 'sanctum')
            ->postJson("/api/users/{$user->id}/products/{$product->id}/colors/{$color->id}/variants/{$variant->id}/favorites");

        // Assert
        $response->assertStatus(201);
        $this->assertDatabaseHas('favorites', [
            'user_id' => $user->id,
            'product_id' => $product->id,
            'color_id' => $color->id,
            'variant_id' => $variant->id,
        ]);
    }
}

