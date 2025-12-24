<?php

namespace Modules\Coupon\Tests\Unit\Services;

use Tests\TestCase;
use Modules\Coupon\Services\Coupon\CouponService;
use Modules\Coupon\Repositories\Interface\Coupon\CouponRepositoryInterface;
use Modules\Coupon\Contracts\CouponUsageRepositoryInterface;
use Modules\Coupon\Entities\Coupon\Coupon;
use Modules\Coupon\Enums\CouponType;
use Modules\Coupon\Enums\CouponStatus;
use Modules\Coupon\Exceptions\Coupon\CouponException;
use Carbon\Carbon;
use Mockery;

/**
 * TC-COU-001 to TC-COU-009: All Coupon Validation and Discount Calculation Tests
 */
class CouponServiceTest extends TestCase
{
    private CouponService $service;
    private $couponRepositoryMock;
    private $couponUsageRepositoryMock;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->couponRepositoryMock = Mockery::mock(CouponRepositoryInterface::class);
        $this->couponUsageRepositoryMock = Mockery::mock(CouponUsageRepositoryInterface::class);
        
        $this->service = new CouponService(
            $this->couponRepositoryMock,
            $this->couponUsageRepositoryMock
        );
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_applies_fixed_coupon_correctly(): void
    {
        // Arrange
        $coupon = Coupon::factory()->make([
            'type' => CouponType::FIXED,
            'discount_amount' => 10.00,
            'status' => CouponStatus::ACTIVE,
            'start_date' => Carbon::now()->subDay(),
            'end_date' => Carbon::now()->addDay(),
        ]);

        $this->couponRepositoryMock
            ->shouldReceive('findCoupon')
            ->with('TEST-001')
            ->once()
            ->andReturn($coupon);

        $this->couponUsageRepositoryMock
            ->shouldReceive('countCouponUsage')
            ->with($coupon->id)
            ->once()
            ->andReturn(0);

        $this->couponUsageRepositoryMock
            ->shouldReceive('countUserCouponUsage')
            ->with($coupon->id, 1)
            ->once()
            ->andReturn(0);

        // Act
        $result = $this->service->applyCouponToOrder('TEST-001', 100.00, 1);

        // Assert
        $this->assertEquals(90.00, $result); // 100 - 10
    }

    public function test_applies_percentage_coupon_correctly(): void
    {
        // Arrange
        $coupon = Coupon::factory()->make([
            'type' => CouponType::PERCENTAGE,
            'discount_amount' => 10, // 10%
            'status' => CouponStatus::ACTIVE,
            'start_date' => Carbon::now()->subDay(),
            'end_date' => Carbon::now()->addDay(),
        ]);

        $this->couponRepositoryMock
            ->shouldReceive('findCoupon')
            ->with('TEST-002')
            ->once()
            ->andReturn($coupon);

        $this->couponUsageRepositoryMock
            ->shouldReceive('countCouponUsage')
            ->with($coupon->id)
            ->once()
            ->andReturn(0);

        $this->couponUsageRepositoryMock
            ->shouldReceive('countUserCouponUsage')
            ->with($coupon->id, 1)
            ->once()
            ->andReturn(0);

        // Act
        $result = $this->service->applyCouponToOrder('TEST-002', 100.00, 1);

        // Assert
        $this->assertEquals(90.00, $result); // 100 - (10% of 100)
    }

    public function test_throws_exception_for_expired_coupon(): void
    {
        // Arrange
        $coupon = Coupon::factory()->make([
            'status' => CouponStatus::ACTIVE,
            'end_date' => Carbon::now()->subDay(), // Expired
        ]);

        $this->couponRepositoryMock
            ->shouldReceive('findCoupon')
            ->with('EXPIRED')
            ->once()
            ->andReturn($coupon);

        // Act & Assert
        $this->expectException(CouponException::class);
        $this->service->applyCoupon('EXPIRED', 1);
    }

    public function test_throws_exception_for_inactive_coupon(): void
    {
        // Arrange
        $coupon = Coupon::factory()->make([
            'status' => CouponStatus::INACTIVE,
        ]);

        $this->couponRepositoryMock
            ->shouldReceive('findCoupon')
            ->with('INACTIVE')
            ->once()
            ->andReturn($coupon);

        // Act & Assert
        $this->expectException(CouponException::class);
        $this->service->applyCoupon('INACTIVE', 1);
    }

    public function test_throws_exception_when_global_usage_limit_exceeded(): void
    {
        // Arrange
        $coupon = Coupon::factory()->make([
            'usage_limit' => 10,
            'status' => CouponStatus::ACTIVE,
            'start_date' => Carbon::now()->subDay(),
            'end_date' => Carbon::now()->addDay(),
        ]);

        $this->couponRepositoryMock
            ->shouldReceive('findCoupon')
            ->with('LIMIT-001')
            ->once()
            ->andReturn($coupon);

        $this->couponUsageRepositoryMock
            ->shouldReceive('countCouponUsage')
            ->with($coupon->id)
            ->once()
            ->andReturn(10); // Already at limit

        // Act & Assert
        $this->expectException(CouponException::class);
        $this->service->applyCoupon('LIMIT-001', 1);
    }

    public function test_throws_exception_when_per_user_usage_limit_exceeded(): void
    {
        // Arrange
        $coupon = Coupon::factory()->make([
            'usage_limit_per_user' => 2,
            'status' => CouponStatus::ACTIVE,
            'start_date' => Carbon::now()->subDay(),
            'end_date' => Carbon::now()->addDay(),
        ]);

        $this->couponRepositoryMock
            ->shouldReceive('findCoupon')
            ->with('LIMIT-002')
            ->once()
            ->andReturn($coupon);

        $this->couponUsageRepositoryMock
            ->shouldReceive('countCouponUsage')
            ->with($coupon->id)
            ->once()
            ->andReturn(0);

        $this->couponUsageRepositoryMock
            ->shouldReceive('countUserCouponUsage')
            ->with($coupon->id, 1)
            ->once()
            ->andReturn(2); // User already used 2 times

        // Act & Assert
        $this->expectException(CouponException::class);
        $this->service->applyCoupon('LIMIT-002', 1);
    }
}

