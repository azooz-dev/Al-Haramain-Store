<?php

namespace Tests\Feature\Coupon;

use Tests\TestCase;
use Tests\Fixtures\OrderFixtures;
use Modules\Coupon\Entities\Coupon\Coupon;
use Modules\Coupon\Services\Coupon\CouponService;
use Modules\Order\Exceptions\Order\OrderException;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * Coupon Service Tests
 */
class CouponServiceTest extends TestCase
{
    use RefreshDatabase;

    private CouponService $couponService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->couponService = app(CouponService::class);
    }

    /**
     * Test fixed discount coupon application
     */
    public function test_fixed_discount_coupon_application(): void
    {
        // Arrange
        $coupon = OrderFixtures::createActiveCouponWithFixedDiscount(50.00);
        $user = \Modules\User\Entities\User::factory()->verified()->create();
        $totalAmount = 200.00;

        // Act
        $newTotal = $this->couponService->applyCouponToOrder(
            $coupon->code,
            $totalAmount,
            $user->id
        );

        // Assert
        $this->assertEquals(150.00, $newTotal); // 200 - 50
    }

    /**
     * Test percentage discount coupon application
     */
    public function test_percentage_discount_coupon_application(): void
    {
        // Arrange
        $coupon = OrderFixtures::createActiveCouponWithPercentageDiscount(20); // 20%
        $user = \Modules\User\Entities\User::factory()->verified()->create();
        $totalAmount = 200.00;

        // Act
        $newTotal = $this->couponService->applyCouponToOrder(
            $coupon->code,
            $totalAmount,
            $user->id
        );

        // Assert
        $this->assertEquals(160.00, $newTotal); // 200 - (200 * 0.20) = 160
    }

    /**
     * Test coupon application fails with invalid coupon code
     */
    public function test_coupon_application_fails_with_invalid_coupon_code(): void
    {
        // Arrange
        $user = \Modules\User\Entities\User::factory()->verified()->create();
        $totalAmount = 200.00;

        // Act & Assert
        $this->expectException(OrderException::class);
        $this->couponService->applyCouponToOrder('INVALID_CODE', $totalAmount, $user->id);
    }

    /**
     * Test coupon application fails with inactive coupon
     */
    public function test_coupon_application_fails_with_inactive_coupon(): void
    {
        // Arrange
        $coupon = Coupon::factory()->inactive()->create();
        $user = \Modules\User\Entities\User::factory()->verified()->create();
        $totalAmount = 200.00;

        // Act & Assert
        $this->expectException(OrderException::class);
        $this->couponService->applyCouponToOrder($coupon->code, $totalAmount, $user->id);
    }

    /**
     * Test coupon application ensures total not negative
     */
    public function test_coupon_application_ensures_total_not_negative(): void
    {
        // Arrange
        $coupon = OrderFixtures::createActiveCouponWithFixedDiscount(500.00); // More than total
        $user = \Modules\User\Entities\User::factory()->verified()->create();
        $totalAmount = 200.00;

        // Act
        $newTotal = $this->couponService->applyCouponToOrder(
            $coupon->code,
            $totalAmount,
            $user->id
        );

        // Assert
        $this->assertEquals(0.00, $newTotal); // Should not be negative
    }
}
