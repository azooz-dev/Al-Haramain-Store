<?php

namespace Modules\Catalog\Database\Factories\Product;

use Modules\Catalog\Entities\Product\Product;
use Modules\Catalog\Entities\Product\ProductColor;
use Modules\Catalog\Entities\Product\ProductVariant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Modules\Catalog\Entities\Product\ProductVariant>
 */
class ProductVariantFactory extends Factory
{
  /**
   * The name of the factory's corresponding model.
   *
   * @var class-string<\Illuminate\Database\Eloquent\Model>
   */
  protected $model = ProductVariant::class;

  /**
   * Define the model's default state.
   *
   * @return array<string, mixed>
   */
  public function definition(): array
  {
    $price = fake()->randomFloat(2, 10, 500);
    $hasDiscount = fake()->boolean(30);
    $discountPrice = $hasDiscount ? fake()->randomFloat(2, 5, $price * 0.8) : null;

    return [
      'product_id' => Product::factory(),
      'color_id' => ProductColor::factory(),
      'size' => fake()->randomElement(['XS', 'S', 'M', 'L', 'XL', 'XXL']),
      'price' => $price,
      'amount_discount_price' => $discountPrice,
      'quantity' => fake()->numberBetween(0, 100),
    ];
  }

  /**
   * Indicate that the variant is in stock.
   */
  public function inStock(): static
  {
    return $this->state(fn(array $attributes) => [
      'quantity' => fake()->numberBetween(1, 100),
    ]);
  }

  /**
   * Indicate that the variant is out of stock.
   */
  public function outOfStock(): static
  {
    return $this->state(fn(array $attributes) => [
      'quantity' => 0,
    ]);
  }

  /**
   * Indicate that the variant has a discount.
   */
  public function withDiscount(): static
  {
    return $this->state(function (array $attributes) {
      $price = $attributes['price'] ?? fake()->randomFloat(2, 10, 500);
      return [
        'amount_discount_price' => fake()->randomFloat(2, 5, $price * 0.8),
      ];
    });
  }

  /**
   * Indicate a specific size.
   */
  public function size(string $size): static
  {
    return $this->state(fn(array $attributes) => [
      'size' => $size,
    ]);
  }
}

