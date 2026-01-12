<?php

namespace Modules\Catalog\Database\Factories\Product;

use Modules\Catalog\Entities\Product\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Modules\Catalog\Entities\Product\Product>
 */
class ProductFactory extends Factory
{
  /**
   * The name of the factory's corresponding model.
   *
   * @var class-string<\Illuminate\Database\Eloquent\Model>
   */
  protected $model = Product::class;

  /**
   * Define the model's default state.
   *
   * @return array<string, mixed>
   */
  public function definition(): array
  {
    return [
      'slug' => $this->faker->unique()->slug(),
      'sku' => $this->faker->unique()->regexify('[A-Z]{2}[0-9]{6}'),
      'quantity' => $this->faker->numberBetween(0, 100),
    ];
  }

  /**
   * Indicate that the product is in stock.
   */
  public function inStock(): static
  {
    return $this->state(fn(array $attributes) => [
      'quantity' => $this->faker->numberBetween(1, 100),
    ]);
  }

  /**
   * Indicate that the product is out of stock.
   */
  public function outOfStock(): static
  {
    return $this->state(fn(array $attributes) => [
      'quantity' => 0,
    ]);
  }

  /**
   * Indicate that the product has low stock.
   */
  public function lowStock(): static
  {
    return $this->state(fn(array $attributes) => [
      'quantity' => $this->faker->numberBetween(1, 10),
    ]);
  }

  /**
   * Indicate a specific SKU.
   */
  public function sku(string $sku): static
  {
    return $this->state(fn(array $attributes) => [
      'sku' => $sku,
    ]);
  }
}

