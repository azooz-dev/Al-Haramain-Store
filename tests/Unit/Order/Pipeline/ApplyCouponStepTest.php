<?php

namespace Tests\Unit\Order\Pipeline;

use Tests\TestCase;
use Modules\Catalog\Entities\Product\Product;
use Modules\Catalog\Entities\Product\ProductColor;
use Modules\Catalog\Entities\Product\ProductVariant;
use App\Models\Coupon\Coupon;
use App\Services\Order\Pipeline\ApplyCouponStep;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * Unit tests for ApplyCouponStep
 */
class ApplyCouponStepTest extends TestCase
{
    use RefreshDatabase;

    private ApplyCouponStep $step;

    protected function setUp(): void
    {
        parent::setUp();
        $this->step = app(ApplyCouponStep::class);
    }

    /**
     * Test fixed discount coupon is applied correctly
     */
    public function test_fixed_discount_coupon_is_applied_correctly(): void
    {
        // Arrange
        $user = \Modules\User\Entities\User::factory()->create(); // Create a real user

        $product = Product::factory()->create();
        $color = ProductColor::factory()->create(['product_id' => $product->id]);
        $variant = ProductVariant::factory()->create([
            'product_id' => $product->id,
            'color_id' => $color->id,
            'quantity' => 100,
            'price' => 100.00,
            'amount_discount_price' => null, // Explicitly no discount
        ]);

        $coupon = Coupon::factory()
            ->active()
            ->fixedDiscount()
            ->create(['discount_amount' => 50.00, 'code' => 'FIXED50']);

        $data = [
            'user_id' => $user->id, // Use the created user's ID
            'coupon_code' => 'FIXED50',
            'items' => [
                [
                    'orderable_type' => Product::class,
                    'variant_id' => $variant->id,
                    'quantity' => 2,
                    'total_price' => 200.00,
                ],
            ],
        ];

        // Act
        $result = $this->step->handle($data, fn($data) => $data);

        // Assert
        $this->assertIsArray($result);
        $this->assertEquals(150.00, $result['total_amount']); // 200 - 50
    }

    /**
     * Test percentage discount coupon is applied correctly
     */
    public function test_percentage_discount_coupon_is_applied_correctly(): void
    {
        // Arrange
        $user = \Modules\User\Entities\User::factory()->create(); // Add this line

        $product = Product::factory()->create();
        $color = ProductColor::factory()->create(['product_id' => $product->id]);
        $variant = ProductVariant::factory()->create([
            'product_id' => $product->id,
            'color_id' => $color->id,
            'quantity' => 100,
            'price' => 100.00,
            'amount_discount_price' => null, // Explicitly no discount
        ]);

        $coupon = Coupon::factory()
            ->active()
            ->percentageDiscount()
            ->create(['discount_amount' => 20, 'code' => 'PERCENT20']);

        $data = [
            'user_id' => $user->id, // Change from 1 to $user->id
            'coupon_code' => $coupon->code,
            'items' => [
                [
                    'orderable_type' => Product::class,
                    'variant_id' => $variant->id,
                    'quantity' => 2,
                    'total_price' => 200.00,
                ],
            ],
        ];

        // Act
        $result = $this->step->handle($data, fn($data) => $data);

        // Assert
        $this->assertIsArray($result);
        $this->assertEquals(160.00, $result['total_amount']); // 200 - (200 * 0.20)
    }
    /**
     * Test order without coupon code
     */
    public function test_order_without_coupon_code(): void
    {
        // Arrange
        $user = \Modules\User\Entities\User::factory()->create(); // Add this line

        $product = Product::factory()->create();
        $color = ProductColor::factory()->create(['product_id' => $product->id]);
        $variant = ProductVariant::factory()->create([
            'product_id' => $product->id,
            'color_id' => $color->id,
            'quantity' => 100,
            'price' => 100.00,
            'amount_discount_price' => null, // Explicitly no discount
        ]);

        $data = [
            'user_id' => $user->id, // Use the created user's ID
            'coupon_code' => '',
            'items' => [
                [
                    'orderable_type' => Product::class,
                    'variant_id' => $variant->id,
                    'quantity' => 2,
                    'total_price' => 200.00,
                ],
            ],
        ];

        // Act
        $result = $this->step->handle($data, fn($data) => $data);

        // Assert
        $this->assertIsArray($result);
        $this->assertEquals(200.00, $result['total_amount']); // No discount applied
    }
}
