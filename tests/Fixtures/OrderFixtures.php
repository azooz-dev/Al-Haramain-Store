<?php

namespace Tests\Fixtures;

use App\Models\User\User;
use Modules\Catalog\Entities\Product\Product;
use Modules\Catalog\Entities\Product\ProductColor;
use Modules\Catalog\Entities\Product\ProductVariant;
use App\Models\Coupon\Coupon;
use App\Models\User\UserAddresses\Address;

/**
 * Test Fixtures - Reusable test data creation
 */
class OrderFixtures
{
    public static function createVerifiedUserWithAddress(): array
    {
        $user = User::factory()->verified()->create();
        $address = Address::factory()->create(['user_id' => $user->id]);
        
        return ['user' => $user, 'address' => $address];
    }

    public static function createProductWithVariantInStock(int $quantity = 100, float $price = 100.00): array
    {
        $product = Product::factory()->create();
        $color = ProductColor::factory()->create(['product_id' => $product->id]);
        $variant = ProductVariant::factory()
            ->create([
                'product_id' => $product->id,
                'color_id' => $color->id,
                'quantity' => $quantity,
                'price' => $price,
            ]);

        return [
            'product' => $product,
            'color' => $color,
            'variant' => $variant,
        ];
    }

    public static function createProductWithVariantOutOfStock(): array
    {
        return self::createProductWithVariantInStock(0, 100.00);
    }

    public static function createActiveCouponWithFixedDiscount(float $discount = 50.00): Coupon
    {
        return Coupon::factory()
            ->active()
            ->fixedDiscount()
            ->create(['discount_amount' => $discount]);
    }

    public static function createActiveCouponWithPercentageDiscount(int $percentage = 20): Coupon
    {
        return Coupon::factory()
            ->active()
            ->percentageDiscount()
            ->create(['discount_amount' => $percentage]);
    }
}

