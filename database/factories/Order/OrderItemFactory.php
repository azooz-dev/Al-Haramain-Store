<?php

namespace Database\Factories\Order;

use App\Models\Order\Order;
use App\Models\Order\OrderItem;
use Modules\Catalog\Entities\Product\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order\OrderItem>
 */
class OrderItemFactory extends Factory
{
  /**
   * Define the model's default state.
   *
   * @return array<string, mixed>
   */
  public function definition(): array
  {
    $price = fake()->randomFloat(2, 10, 500);
    $quantity = fake()->numberBetween(1, 5);
    $hasDiscount = fake()->boolean(30);
    $discountPrice = $hasDiscount ? fake()->randomFloat(2, 1, $price * 0.3) : 0;

    return [
      'order_id' => Order::factory(),
      'product_id' => Product::factory(),
      'quantity' => $quantity,
      'total_price' => $price,
      'amount_discount_price' => $discountPrice,
    ];
  }

  /**
   * Indicate that the item has a discount.
   */
  public function withDiscount(): static
  {
    return $this->state(function (array $attributes) {
      $price = $attributes['total_price'] ?? fake()->randomFloat(2, 10, 500);
      return [
        'amount_discount_price' => fake()->randomFloat(2, 1, $price * 0.3),
      ];
    });
  }

  /**
   * Indicate a specific quantity.
   */
  public function quantity(int $quantity): static
  {
    return $this->state(fn(array $attributes) => [
      'quantity' => $quantity,
    ]);
  }
}
