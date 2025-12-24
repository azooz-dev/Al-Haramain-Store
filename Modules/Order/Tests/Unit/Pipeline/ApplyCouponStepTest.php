<?php

namespace Modules\Order\Tests\Unit\Pipeline;

use Tests\TestCase;
use Modules\Order\Services\Order\Pipeline\ApplyCouponStep;
use Modules\Coupon\Contracts\CouponServiceInterface;
use Modules\Catalog\Contracts\ProductVariantServiceInterface;
use Modules\Coupon\Entities\Coupon\Coupon;
use Modules\Coupon\Exceptions\CouponException;
use Mockery;

/**
 * TC-ORD-005: Order Creation - With Valid Coupon
 * TC-ORD-006: Order Creation - With Invalid Coupon
 */
class ApplyCouponStepTest extends TestCase
{
    private ApplyCouponStep $step;
    private $couponServiceMock;
    private $variantServiceMock;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->couponServiceMock = Mockery::mock(CouponServiceInterface::class);
        $this->variantServiceMock = Mockery::mock(ProductVariantServiceInterface::class);
        $this->step = new ApplyCouponStep($this->couponServiceMock, $this->variantServiceMock);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_applies_valid_coupon_successfully(): void
    {
        // Arrange
        $coupon = Coupon::factory()->make(['id' => 1, 'code' => 'SAVE10']);
        $data = [
            'user_id' => 1,
            'coupon_code' => 'SAVE10',
            'items' => [],
        ];

        $this->variantServiceMock
            ->shouldReceive('calculateTotalOrderPrice')
            ->with($data['items'])
            ->once()
            ->andReturn(100.00);

        $this->couponServiceMock
            ->shouldReceive('applyCoupon')
            ->with('SAVE10', 1)
            ->once()
            ->andReturn($coupon);

        $this->couponServiceMock
            ->shouldReceive('applyCouponToOrder')
            ->with('SAVE10', 100.00, 1)
            ->once()
            ->andReturn(90.00);

        // Act
        $result = $this->step->handle($data, function ($data) {
            return $data;
        });

        // Assert
        $this->assertEquals(90.00, $result['total_amount']);
        $this->assertEquals(1, $result['coupon_id']);
    }

    public function test_throws_exception_when_coupon_invalid(): void
    {
        // Arrange
        $data = [
            'user_id' => 1,
            'coupon_code' => 'INVALID',
            'items' => [],
        ];

        $this->variantServiceMock
            ->shouldReceive('calculateTotalOrderPrice')
            ->with($data['items'])
            ->once()
            ->andReturn(100.00);

        $this->couponServiceMock
            ->shouldReceive('applyCoupon')
            ->with('INVALID', 1)
            ->once()
            ->andThrow(new CouponException('Invalid coupon'));

        // Act & Assert
        $this->expectException(CouponException::class);

        $this->step->handle($data, function ($data) {
            return $data;
        });
    }

    public function test_skips_coupon_when_not_provided(): void
    {
        // Arrange
        $data = [
            'user_id' => 1,
            'items' => [],
        ];

        $this->variantServiceMock
            ->shouldReceive('calculateTotalOrderPrice')
            ->with($data['items'])
            ->once()
            ->andReturn(100.00);

        // Act
        $result = $this->step->handle($data, function ($data) {
            return $data;
        });

        // Assert
        $this->assertEquals(100.00, $result['total_amount']);
        $this->assertArrayNotHasKey('coupon_id', $result);
    }
}

