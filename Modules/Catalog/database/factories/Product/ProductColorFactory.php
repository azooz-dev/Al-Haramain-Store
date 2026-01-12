<?php

namespace Modules\Catalog\Database\Factories\Product;

use Modules\Catalog\Entities\Product\Product;
use Modules\Catalog\Entities\Product\ProductColor;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Modules\Catalog\Entities\Product\ProductColor>
 */
class ProductColorFactory extends Factory
{
  /**
   * The name of the factory's corresponding model.
   *
   * @var class-string<\Illuminate\Database\Eloquent\Model>
   */
  protected $model = ProductColor::class;

  /**
   * Define the model's default state.
   *
   * @return array<string, mixed>
   */
  public function definition(): array
  {
    return [
      'product_id' => Product::factory(),
      'color_code' => $this->faker->randomElement(['red', 'blue', 'green', 'black', 'white', 'yellow', 'purple', 'orange', 'pink', 'brown']),
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

