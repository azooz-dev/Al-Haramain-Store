<?php

namespace Database\Factories\Product;

use App\Models\Product\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product\Product>
 */
class ProductFactory extends Factory
{
  /**
   * Define the model's default state.
   *
   * @return array<string, mixed>
   */
  public function definition(): array
  {
    return [
      'slug' => fake()->unique()->slug(),
      'sku' => fake()->unique()->regexify('[A-Z]{2}[0-9]{6}'),
      'quantity' => fake()->numberBetween(0, 100),
    ];
  }

  /**
   * Indicate that the product is in stock.
   */
  public function inStock(): static
  {
    return $this->state(fn(array $attributes) => [
      'quantity' => fake()->numberBetween(1, 100),
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
      'quantity' => fake()->numberBetween(1, 10),
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
