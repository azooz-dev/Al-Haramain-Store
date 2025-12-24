<?php

namespace Modules\Order\Tests\Integration;

use Tests\TestCase;
use Tests\Support\Builders\OrderTestDataBuilder;
use Modules\Order\Entities\Order\Order;
use Modules\Coupon\Entities\Coupon\Coupon;
use Modules\Order\Repositories\Eloquent\Order\CouponUsageRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OrderCouponIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        \Spatie\Permission\Models\Role::firstOrCreate(
            ['name' => 'super_admin', 'guard_name' => 'admin']
        );
    }

    public function test_order_applies_coupon_discount_correctly(): void
    {
        // Arrange
        $coupon = Coupon::factory()
            ->active()
            ->percentageDiscount()
            ->create(['discount_amount' => 20]); // 20% discount

        $builder = OrderTestDataBuilder::create()
            ->withVerifiedUser()
            ->withProduct(['quantity' => 10], ['quantity' => 10, 'price' => 100.00])
            ->withActiveCoupon(['code' => $coupon->code, 'discount_amount' => 20]);

        $orderData = $builder->buildOrderData();
        $expectedTotal = 80.00; // 100 - 20%

        // Act
        $response = $this->postJson('/api/orders', $orderData);

        // Assert
        $response->assertStatus(201);
        
        $order = Order::where('user_id', $builder->getUser()->id)->first();
        $this->assertNotNull($order->coupon_id);
        $this->assertEquals($coupon->id, $order->coupon_id);
        $this->assertEquals($expectedTotal, (float)$order->total_amount);
    }

    public function test_order_tracks_coupon_usage(): void
    {
        // Arrange
        $coupon = Coupon::factory()
            ->active()
            ->create(['usage_limit' => 10]);

        $builder = OrderTestDataBuilder::create()
            ->withVerifiedUser()
            ->withProduct(['quantity' => 10], ['quantity' => 10])
            ->withActiveCoupon(['code' => $coupon->code]);

        $orderData = $builder->buildOrderData();

        // Act
        $response = $this->postJson('/api/orders', $orderData);

        // Assert
        $response->assertStatus(201);
        
        $couponUsageRepo = new CouponUsageRepository();
        $usageCount = $couponUsageRepo->countCouponUsage($coupon->id);
        
        $this->assertGreaterThan(0, $usageCount);
    }
}

