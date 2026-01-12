<?php

namespace Modules\Order\Database\Factories\OrderItem;

use Modules\Order\Entities\Order\Order;
use Modules\Order\Entities\OrderItem\OrderItem;
use Modules\Catalog\Entities\Product\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Modules\Order\Entities\OrderItem\OrderItem>
 */
class OrderItemFactory extends Factory
{
  /**
   * The name of the factory's corresponding model.
   *
   * @var class-string<\Illuminate\Database\Eloquent\Model>
   */
  protected $model = OrderItem::class;

  /**
   * Define the model's default state.
   *
   * @return array<string, mixed>
   */
  public function definition(): array
  {
    $price = $this->faker->randomFloat(2, 10, 500);
    $quantity = $this->faker->numberBetween(1, 5);
    $hasDiscount = $this->faker->boolean(30);
    $discountPrice = $hasDiscount ? $this->faker->randomFloat(2, 1, $price * 0.3) : 0;
    $product = Product::factory()->create();

    return [
      'order_id' => Order::factory(),
      'orderable_type' => Product::class,
      'orderable_id' => $product->id,
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
      $price = $attributes['total_price'] ?? $this->faker->randomFloat(2, 10, 500);
      return [
        'amount_discount_price' => $this->faker->randomFloat(2, 1, $price * 0.3),
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
