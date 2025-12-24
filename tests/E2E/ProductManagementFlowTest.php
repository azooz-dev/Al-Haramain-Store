<?php

namespace Tests\E2E;

use Tests\TestCase;
use Modules\Admin\Entities\Admin;
use Modules\Catalog\Entities\Product\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * E2E-05: Product Management Flow
 * 
 * Tests the complete flow for product management:
 * 1. Admin views products list
 * 2. Admin creates new product
 * 3. Admin updates product
 * 4. Product changes are reflected in catalog
 */
class ProductManagementFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_product_management_flow(): void
    {
        // Arrange
        $admin = Admin::factory()->create();

        // Step 1: Admin views products list
        $productsResponse = $this->actingAs($admin, 'admin')
            ->getJson('/api/admin/products');

        $productsResponse->assertStatus(200);

        // Step 2: Admin creates new product
        $productData = [
            'name' => 'Test Product',
            'price' => 99.99,
            'quantity' => 10,
        ];

        $createResponse = $this->actingAs($admin, 'admin')
            ->postJson('/api/admin/products', $productData);

        $createResponse->assertStatus(201);
        $productId = $createResponse->json('data.id');

        // Step 3: Verify product was created
        $product = Product::find($productId);
        $this->assertNotNull($product);
        $this->assertEquals('Test Product', $product->name);

        // Step 4: Admin updates product
        $updateData = [
            'name' => 'Updated Product Name',
            'price' => 149.99,
        ];

        $updateResponse = $this->actingAs($admin, 'admin')
            ->putJson("/api/admin/products/{$productId}", $updateData);

        $updateResponse->assertStatus(200);

        // Step 5: Verify product was updated
        $product->refresh();
        $this->assertEquals('Updated Product Name', $product->name);
        $this->assertEquals(149.99, $product->price);
    }
}

