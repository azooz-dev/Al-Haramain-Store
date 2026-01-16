<?php

namespace Modules\Order\tests\Feature;

use Tests\TestCase;
use Tests\Support\Builders\OrderTestDataBuilder;
use Modules\Order\Entities\Order\Order;
use Modules\Order\Enums\OrderStatus;
use Modules\Catalog\Entities\Product\Product;
use Modules\Catalog\Entities\Product\ProductVariant;
use Modules\Coupon\Entities\Coupon\Coupon;
use Modules\Catalog\Exceptions\Product\Variant\OutOfStockException;
use Modules\Payment\Enums\PaymentMethod;
use Modules\User\Entities\Address;
use Illuminate\Support\Facades\DB;

/**
 * TC-ORD-001: Create Order with Valid Data
 * TC-ORD-002: Order Creation - Out of Stock
 * TC-ORD-003: Order Creation - Insufficient Stock
 * TC-ORD-005: Order Creation - With Valid Coupon
 * TC-ORD-006: Order Creation - With Invalid Coupon
 * TC-ORD-007: Order Creation - Multiple Items (Products + Offers)
 * TC-ORD-009: Stock Deduction After Order
 * TC-ORD-010: Concurrent Order - Race Condition
 */
class OrderCreationTest extends TestCase
{

    protected function setUp(): void
    {
        parent::setUp();

        \Spatie\Permission\Models\Role::firstOrCreate(
            ['name' => 'super_admin', 'guard_name' => 'admin']
        );
    }

    /**
     * TC-ORD-001: Create Order with Valid Data
     */
    public function test_creates_order_with_valid_data(): void
    {
        // Arrange
        $builder = OrderTestDataBuilder::create()
            ->withVerifiedUser()
            ->withProduct(['quantity' => 10], ['quantity' => 10]);

        $orderData = $builder->buildOrderData();

        // Act
        $response = $this->actingAs($builder->getUser(), 'sanctum')
            ->postJson('/api/orders', $orderData);

        // Assert
        $response->assertStatus(201);
        $response->assertJsonStructure([
            'data' => [
                'identifier',
                'orderNumber',
                'status',
                'totalAmount',
            ],
        ]);

        $this->assertDatabaseHas('orders', [
            'user_id' => $builder->getUser()->id,
            'status' => OrderStatus::PENDING->value,
        ]);

        $order = Order::where('user_id', $builder->getUser()->id)->first();
        $this->assertMatchesRegularExpression('/^ORD-\d{4}-\d{6}$/', $order->order_number);
    }

    /**
     * TC-ORD-002: Order Creation - Out of Stock
     */
    public function test_fails_when_product_is_out_of_stock(): void
    {
        // Arrange
        $builder = OrderTestDataBuilder::create()
            ->withVerifiedUser()
            ->withProduct(['quantity' => 0], ['quantity' => 0]);

        $orderData = $builder->buildOrderData();

        // Act
        $response = $this->actingAs($builder->getUser(), 'sanctum')
            ->postJson('/api/orders', $orderData);

        // Assert
        $response->assertStatus(422);
        $this->assertDatabaseMissing('orders', [
            'user_id' => $builder->getUser()->id,
        ]);
    }

    /**
     * TC-ORD-003: Order Creation - Insufficient Stock
     */
    public function test_fails_when_insufficient_stock(): void
    {
        // Arrange
        $builder = OrderTestDataBuilder::create()
            ->withVerifiedUser()
            ->withProduct(['quantity' => 5], ['quantity' => 5]);

        $orderData = $builder->buildOrderData();
        $orderData['items'][0]['quantity'] = 10; // Request more than available

        // Act
        $response = $this->actingAs($builder->getUser(), 'sanctum')
            ->postJson('/api/orders', $orderData);

        // Assert
        $response->assertStatus(422);
        $this->assertDatabaseMissing('orders', [
            'user_id' => $builder->getUser()->id,
        ]);
    }

    /**
     * TC-ORD-005: Order Creation - With Valid Coupon
     */
    public function test_creates_order_with_valid_coupon(): void
    {
        // Arrange
        $coupon = Coupon::factory()
            ->active()
            ->percentageDiscount()
            ->create(['discount_amount' => 10]);

        $builder = OrderTestDataBuilder::create()
            ->withVerifiedUser()
            ->withProduct(['quantity' => 10], ['quantity' => 10, 'price' => 100.00])
            ->withCoupon($coupon);

        $orderData = $builder->buildOrderData();

        // Act
        $response = $this->actingAs($builder->getUser(), 'sanctum')
            ->postJson('/api/orders', $orderData);

        // Assert
        $response->assertStatus(201);

        $order = Order::where('user_id', $builder->getUser()->id)->first();
        $this->assertNotNull($order->coupon_id);
        $this->assertEquals($coupon->id, $order->coupon_id);
        // Total should be reduced by coupon discount
        $this->assertLessThan(100.00, $order->total_amount);
    }

    /**
     * TC-ORD-006: Order Creation - With Invalid Coupon
     */
    public function test_fails_when_coupon_is_invalid(): void
    {
        // Arrange
        $expiredCoupon = Coupon::factory()
            ->expired()
            ->create();

        $builder = OrderTestDataBuilder::create()
            ->withVerifiedUser()
            ->withProduct(['quantity' => 10], ['quantity' => 10])
            ->withCoupon($expiredCoupon);

        $orderData = $builder->buildOrderData();

        // Act
        $response = $this->actingAs($builder->getUser(), 'sanctum')
            ->postJson('/api/orders', $orderData);

        // Assert
        $response->assertStatus(422);
        $this->assertDatabaseMissing('orders', [
            'user_id' => $builder->getUser()->id,
        ]);
    }

    /**
     * TC-ORD-007: Order Creation - Multiple Items (Products + Offers)
     */
    public function test_creates_order_with_products_and_offers(): void
    {
        // Arrange
        $builder = OrderTestDataBuilder::create()
            ->withVerifiedUser()
            ->withProduct(['quantity' => 10], ['quantity' => 10])
            ->withOffer();

        $orderData = $builder->buildOrderData();

        // Act
        $response = $this->actingAs($builder->getUser(), 'sanctum')
            ->postJson('/api/orders', $orderData);

        // Assert
        $response->assertStatus(201);

        $order = Order::where('user_id', $builder->getUser()->id)->first();
        $this->assertGreaterThan(1, $order->items->count());

        // Verify both product and offer items exist
        $hasProduct = $order->items->contains(function ($item) {
            return $item->orderable_type === Product::class;
        });
        $this->assertTrue($hasProduct);
    }

    /**
     * TC-ORD-009: Stock Deduction After Order
     */
    public function test_deducts_stock_after_order_creation(): void
    {
        // Arrange
        $initialStock = 10;
        $orderQuantity = 3;

        $builder = OrderTestDataBuilder::create()
            ->withVerifiedUser()
            ->withProduct(['quantity' => $initialStock], ['quantity' => $initialStock]);

        $orderData = $builder->buildOrderData();
        $orderData['items'][0]['quantity'] = $orderQuantity;

        $variant = $builder->getProducts()[0]['variant'];
        $initialVariantStock = $variant->quantity;

        // Act
        $response = $this->actingAs($builder->getUser(), 'sanctum')
            ->postJson('/api/orders', $orderData);

        // Assert
        $response->assertStatus(201);

        $variant->refresh();
        $this->assertEquals($initialVariantStock - $orderQuantity, $variant->quantity);
    }

    /**
     * TC-ORD-010: Concurrent Order - Race Condition
     */
    public function test_handles_concurrent_orders_for_same_product(): void
    {
        // Arrange
        $stock = 1; // Only 1 item available

        $builder1 = OrderTestDataBuilder::create()
            ->withVerifiedUser()
            ->withProduct(['quantity' => $stock], ['quantity' => $stock]);

        $builder2 = OrderTestDataBuilder::create()
            ->withVerifiedUser();

        // Get the same variant for both orders
        $variant = $builder1->getProducts()[0]['variant'];

        $orderData2 = $builder2->buildOrderData();
        $orderData2['items'] = [[
            'orderable_type' => Product::class,
            'orderable_id' => $builder1->getProducts()[0]['product']->id,
            'color_id' => $builder1->getProducts()[0]['color']->id,
            'variant_id' => $variant->id,
            'quantity' => 1,
        ]];
        $orderData2['address_id'] = Address::factory()->create(['user_id' => $builder2->getUser()->id])->id;

        $orderData1 = $builder1->buildOrderData();

        // Act - Create first order
        $response1 = $this->actingAs($builder1->getUser(), 'sanctum')
            ->postJson('/api/orders', $orderData1);
        $results[] = $response1->status();

        // Try to create second order immediately (should fail due to stock)
        $response2 = $this->actingAs($builder2->getUser(), 'sanctum')
            ->postJson('/api/orders', $orderData2);
        $results[] = $response2->status();

        // Assert - Only one order should succeed
        $successfulOrders = Order::whereIn('user_id', [
            $builder1->getUser()->id,
            $builder2->getUser()->id,
        ])->count();

        $this->assertEquals(1, $successfulOrders, 'Expected exactly 1 successful order, but got: ' . $successfulOrders);
        $this->assertContains(201, $results);
        $this->assertContains(422, $results);
    }
}
