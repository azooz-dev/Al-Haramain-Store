<?php

namespace Database\Factories\Product;

use App\Models\Product\Product;
use App\Models\Product\ProductColor;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product\ProductColor>
 */
class ProductColorFactory extends Factory
{
  /**
   * Define the model's default state.
   *
   * @return array<string, mixed>
   */
  public function definition(): array
  {
    return [
      'product_id' => Product::factory(),
      'color_code' => fake()->randomElement(['red', 'blue', 'green', 'black', 'white', 'yellow', 'purple', 'orange', 'pink', 'brown']),
    ];
  }

  /**
   * Indicate a specific color.
   */
  public function color(string $colorCode): static
  {
    return $this->state(fn(array $attributes) => [
      'color_code' => $colorCode,
    ]);
  }
}
