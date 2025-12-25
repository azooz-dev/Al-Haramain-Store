<?php

namespace Modules\Order\Tests\Integration;

use Tests\TestCase;
use Tests\Support\Builders\OrderTestDataBuilder;
use Modules\Order\Entities\Order\Order;
use Modules\Catalog\Entities\Product\ProductVariant;
use Modules\Catalog\Entities\Product\Product;

class OrderStockIntegrationTest extends TestCase
{

    protected function setUp(): void
    {
        parent::setUp();
        
        \Spatie\Permission\Models\Role::firstOrCreate(
            ['name' => 'super_admin', 'guard_name' => 'admin']
        );
    }

    public function test_order_deducts_stock_from_variant_and_product(): void
    {
        // Arrange
        $initialVariantStock = 10;
        $initialProductStock = 50;
        $orderQuantity = 3;

        $builder = OrderTestDataBuilder::create()
            ->withVerifiedUser()
            ->withProduct(
                ['quantity' => $initialProductStock],
                ['quantity' => $initialVariantStock]
            );

        $orderData = $builder->buildOrderData();
        $orderData['items'][0]['quantity'] = $orderQuantity;

        $variant = $builder->getProducts()[0]['variant'];
        $product = $builder->getProducts()[0]['product'];

        $initialVariantQty = $variant->quantity;
        $initialProductQty = $product->quantity;

        // Act
        $response = $this->actingAs($builder->getUser(), 'sanctum')
            ->postJson('/api/orders', $orderData);

        // Assert
        $response->assertStatus(201);
        
        $variant->refresh();
        $product->refresh();
        
        $this->assertEquals($initialVariantQty - $orderQuantity, $variant->quantity);
        $this->assertEquals($initialProductQty - $orderQuantity, $product->quantity);
    }

    public function test_order_prevents_stock_deduction_when_insufficient(): void
    {
        // Arrange
        $stock = 2;
        $requestedQuantity = 5;

        $builder = OrderTestDataBuilder::create()
            ->withVerifiedUser()
            ->withProduct(['quantity' => $stock], ['quantity' => $stock]);

        $orderData = $builder->buildOrderData();
        $orderData['items'][0]['quantity'] = $requestedQuantity;

        $variant = $builder->getProducts()[0]['variant'];
        $initialStock = $variant->quantity;

        // Act
        $response = $this->actingAs($builder->getUser(), 'sanctum')
            ->postJson('/api/orders', $orderData);

        // Assert
        $response->assertStatus(422);
        
        $variant->refresh();
        $this->assertEquals($initialStock, $variant->quantity); // Stock unchanged
    }
}

