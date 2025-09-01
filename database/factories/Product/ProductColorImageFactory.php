<?php

namespace Database\Factories\Product;

use App\Models\Product\ProductColor;
use App\Models\Product\ProductColorImage;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product\ProductColorImage>
 */
class ProductColorImageFactory extends Factory
{
  /**
   * Define the model's default state.
   *
   * @return array<string, mixed>
   */
  public function definition(): array
  {
    return [
      'product_color_id' => ProductColor::factory(),
      'image_url' => fake()->imageUrl(640, 480, 'product'),
      'alt_text' => fake()->sentence(),
    ];
  }

  /**
   * Indicate a specific image URL.
   */
  public function imageUrl(string $url): static
  {
    return $this->state(fn(array $attributes) => [
      'image_url' => $url,
    ]);
  }
}
