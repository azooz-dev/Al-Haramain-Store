<?php

namespace Tests\Feature\Order;

use Tests\TestCase;
use App\Models\Order\Order;
use Modules\Catalog\Entities\Product\Product;
use Tests\Fixtures\OrderFixtures;
use App\Services\Order\OrderService;
use Tests\Support\Builders\OrderTestDataBuilder;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * Enterprise-grade Order Creation Tests
 */
class OrderCreationTest extends TestCase
{
    use RefreshDatabase;

    private OrderService $orderService;

    protected function setUp(): void
    {
        parent::setUp();

        \Spatie\Permission\Models\Role::firstOrCreate(
            ['name' => 'super_admin', 'guard_name' => 'admin']
        );

        $this->orderService = app(OrderService::class);
    }

    /**
     * Test successful order creation with single product
     * 
     */
    public function test_order_creation_succeeds_with_valid_product_data(): void
    {
        // Arrange (AAA Pattern)
        $builder = OrderTestDataBuilder::create()
            ->withVerifiedUser()
            ->withUserAddress()
            ->withProduct([], ['quantity' => 100, 'price' => 150.00]);

        $orderData = $builder->buildOrderData();
        $variant = $builder->getProducts()[0]['variant'];
        $initialStock = $variant->quantity;

        // Act
        $result = $this->orderService->storeOrder($orderData);

        // Assert
        $this->assertNotInstanceOf(\Illuminate\Http\JsonResponse::class, $result);
        $this->assertInstanceOf(\App\Http\Resources\Order\OrderApiResource::class, $result);

        $this->assertDatabaseHas('orders', [
            'user_id' => $orderData['user_id'],
            'address_id' => $orderData['address_id'],
            'payment_method' => Order::PAYMENT_METHOD_CASH_ON_DELIVERY,
            'status' => Order::PENDING,
        ]);

        $order = Order::latest()->first();
        $this->assertEquals(150.00, (float) $order->total_amount);
        $this->assertCount(1, $order->items);

        // Verify stock decremented
        $variant->refresh();
        $this->assertEquals($initialStock - 1, $variant->quantity);
    }

    /**
     * Test order creation fails when user is not verified
     * 
     */
    public function test_order_creation_fails_when_user_is_not_verified(): void
    {
        // Arrange
        $builder = OrderTestDataBuilder::create()
            ->withUnverifiedUser()
            ->withUserAddress()
            ->withProduct([], ['quantity' => 100, 'price' => 100.00]);

        $orderData = $builder->buildOrderData();

        // Act
        $result = $this->orderService->storeOrder($orderData);

        // Assert
        $this->assertInstanceOf(\Illuminate\Http\JsonResponse::class, $result);
        $responseData = json_decode($result->getContent(), true);
        $this->assertEquals('error', $responseData['status']);
        $this->assertEquals(403, $result->getStatusCode());

        // Verify no order was created
        $this->assertDatabaseMissing('orders', ['user_id' => $orderData['user_id']]);
    }

    /**
     * Test order creation fails with insufficient stock
     * 
     */
    public function test_order_creation_fails_when_stock_is_insufficient(): void
    {
        // Arrange
        $builder = OrderTestDataBuilder::create()
            ->withVerifiedUser()
            ->withUserAddress()
            ->withProduct([], ['quantity' => 5, 'price' => 100.00]);

        $orderData = $builder->buildOrderData();
        $orderData['items'][0]['quantity'] = 10; // Request more than available

        $variant = $builder->getProducts()[0]['variant'];
        $initialStock = $variant->quantity;
        $initialOrderCount = Order::count();

        // Act
        $result = $this->orderService->storeOrder($orderData);

        // Assert
        $this->assertInstanceOf(\Illuminate\Http\JsonResponse::class, $result);
        $responseData = json_decode($result->getContent(), true);
        $this->assertEquals('error', $responseData['status']);

        // Verify transaction rollback
        $this->assertEquals($initialOrderCount, Order::count());
        $variant->refresh();
        $this->assertEquals($initialStock, $variant->quantity);
    }

    /**
     * Test inventory is correctly decremented after successful order
     * 
     */
    public function test_inventory_is_decremented_after_successful_order_creation(): void
    {
        // Arrange
        $fixture = OrderFixtures::createProductWithVariantInStock(50, 75.00);
        $userData = OrderFixtures::createVerifiedUserWithAddress();

        $orderData = [
            'user_id' => $userData['user']->id,
            'address_id' => $userData['address']->id,
            'payment_method' => Order::PAYMENT_METHOD_CASH_ON_DELIVERY,
            'items' => [
                [
                    'orderable_type' => Product::class,
                    'orderable_id' => $fixture['product']->id,
                    'color_id' => $fixture['color']->id,
                    'variant_id' => $fixture['variant']->id,
                    'quantity' => 7,
                ],
            ],
        ];

        $initialQuantity = $fixture['variant']->quantity;

        // Act
        $this->orderService->storeOrder($orderData);

        // Assert
        $fixture['variant']->refresh();
        $this->assertEquals($initialQuantity - 7, $fixture['variant']->quantity);
    }

    /**
     * Test coupon discount is correctly applied to order total
     * 
     */
    public function test_fixed_discount_coupon_is_applied_to_order_total(): void
    {
        // Arrange
        $coupon = OrderFixtures::createActiveCouponWithFixedDiscount(75.00);
        $builder = OrderTestDataBuilder::create()
            ->withVerifiedUser()
            ->withUserAddress()
            ->withProduct([], ['quantity' => 100, 'price' => 200.00])
            ->withCoupon($coupon);

        $orderData = $builder->buildOrderData();

        // Act
        $this->orderService->storeOrder($orderData);

        // Assert
        $order = Order::latest()->first();
        $this->assertEquals(125.00, (float) $order->total_amount); // 200 - 75 = 125
        $this->assertEquals($coupon->id, $order->coupon_id);
    }

    /**
     * Test percentage discount coupon is correctly calculated
     * 
     */
    public function test_percentage_discount_coupon_is_correctly_calculated(): void
    {
        // Arrange
        $coupon = OrderFixtures::createActiveCouponWithPercentageDiscount(25); // 25%
        $builder = OrderTestDataBuilder::create()
            ->withVerifiedUser()
            ->withUserAddress()
            ->withProduct([], ['quantity' => 100, 'price' => 200.00])
            ->withCoupon($coupon);

        $orderData = $builder->buildOrderData();

        // Act
        $this->orderService->storeOrder($orderData);

        // Assert
        $order = Order::latest()->first();
        $expectedTotal = 200.00 - (200.00 * 0.25); // 200 - 50 = 150
        $this->assertEquals($expectedTotal, (float) $order->total_amount);
    }

    /**
     * Test order creation with multiple products
     * 
     */
    public function test_order_creation_succeeds_with_multiple_products(): void
    {
        // Arrange
        $builder = OrderTestDataBuilder::create()
            ->withVerifiedUser()
            ->withUserAddress()
            ->withProduct([], ['quantity' => 100, 'price' => 100.00])
            ->withProduct([], ['quantity' => 50, 'price' => 200.00])
            ->withProduct([], ['quantity' => 75, 'price' => 150.00]);

        $orderData = $builder->buildOrderData();

        // Act
        $result = $this->orderService->storeOrder($orderData);

        // Assert
        $this->assertNotInstanceOf(\Illuminate\Http\JsonResponse::class, $result);

        $order = Order::latest()->first();
        $this->assertCount(3, $order->items);
        $this->assertEquals(450.00, (float) $order->total_amount); // 100 + 200 + 150
    }

    /**
     * Test order creation with offers
     * 
     */
    public function test_order_creation_succeeds_with_offer_items(): void
    {
        // Arrange
        $productData = OrderFixtures::createProductWithVariantInStock(100, 100.00);
        $offer = \App\Models\Offer\Offer::factory()
            ->active()
            ->create(['offer_price' => 80.00]);

        \App\Models\Offer\OfferProduct::create([
            'offer_id' => $offer->id,
            'product_id' => $productData['product']->id,
            'product_color_id' => $productData['color']->id,
            'product_variant_id' => $productData['variant']->id,
            'quantity' => 1,
        ]);

        $userData = OrderFixtures::createVerifiedUserWithAddress();

        $orderData = [
            'user_id' => $userData['user']->id,
            'address_id' => $userData['address']->id,
            'payment_method' => Order::PAYMENT_METHOD_CASH_ON_DELIVERY,
            'items' => [
                [
                    'orderable_type' => \App\Models\Offer\Offer::class,
                    'orderable_id' => $offer->id,
                    'quantity' => 1,
                ],
            ],
        ];

        // Act
        $result = $this->orderService->storeOrder($orderData);

        // Assert
        $this->assertNotInstanceOf(\Illuminate\Http\JsonResponse::class, $result);

        $order = Order::latest()->first();
        $this->assertEquals(80.00, (float) $order->total_amount);
        $this->assertCount(1, $order->items);
    }

    /**
     * Test order number is auto-generated
     * 
     */
    public function test_order_number_is_auto_generated_on_creation(): void
    {
        // Arrange
        $builder = OrderTestDataBuilder::create()
            ->withVerifiedUser()
            ->withUserAddress()
            ->withProduct([], ['quantity' => 100, 'price' => 100.00]);

        $orderData = $builder->buildOrderData();

        // Act
        $this->orderService->storeOrder($orderData);

        // Assert
        $order = Order::latest()->first();
        $this->assertNotNull($order->order_number);
        $this->assertStringStartsWith('ORD-', $order->order_number);
    }

    /**
     * Test order status is set to pending by default
     * 
     */
    public function test_order_status_is_set_to_pending_by_default(): void
    {
        // Arrange
        $builder = OrderTestDataBuilder::create()
            ->withVerifiedUser()
            ->withUserAddress()
            ->withProduct([], ['quantity' => 100, 'price' => 100.00]);

        $orderData = $builder->buildOrderData();

        // Act
        $this->orderService->storeOrder($orderData);

        // Assert
        $order = Order::latest()->first();
        $this->assertEquals(Order::PENDING, $order->status);
    }

    /**
     * Test order creation with mixed products and offers
     * 
     */
    public function test_order_creation_succeeds_with_mixed_products_and_offers(): void
    {
        // Arrange
        $productData = OrderFixtures::createProductWithVariantInStock(100, 100.00);
        $offer = \App\Models\Offer\Offer::factory()
            ->active()
            ->create(['offer_price' => 80.00]);

        \App\Models\Offer\OfferProduct::create([
            'offer_id' => $offer->id,
            'product_id' => $productData['product']->id,
            'product_color_id' => $productData['color']->id,
            'product_variant_id' => $productData['variant']->id,
            'quantity' => 1,
        ]);

        $builder = OrderTestDataBuilder::create()
            ->withVerifiedUser()
            ->withUserAddress()
            ->withProduct([], ['quantity' => 100, 'price' => 150.00]);

        $orderData = $builder->buildOrderData();
        $orderData['items'][] = [
            'orderable_type' => \App\Models\Offer\Offer::class,
            'orderable_id' => $offer->id,
            'quantity' => 1,
            'variant_id' => null,
            'color_id' => null,
        ];

        // Act
        $result = $this->orderService->storeOrder($orderData);

        // Assert
        $this->assertNotInstanceOf(\Illuminate\Http\JsonResponse::class, $result);

        $order = Order::latest()->first();
        $this->assertCount(2, $order->items);
        $this->assertEquals(230.00, (float) $order->total_amount); // 150 + 80
    }
}
