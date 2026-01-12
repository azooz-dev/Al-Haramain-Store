<?php

namespace Modules\Catalog\Database\Factories\Product;

use Modules\Catalog\Entities\Product\ProductColor;
use Modules\Catalog\Entities\Product\ProductColorImage;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Modules\Catalog\Entities\Product\ProductColorImage>
 */
class ProductColorImageFactory extends Factory
{
  /**
   * The name of the factory's corresponding model.
   *
   * @var class-string<\Illuminate\Database\Eloquent\Model>
   */
  protected $model = ProductColorImage::class;

  /**
   * Define the model's default state.
   *
   * @return array<string, mixed>
   */
  public function definition(): array
  {
    return [
      'product_color_id' => ProductColor::factory(),
      'image_url' => $this->faker->imageUrl(640, 480, 'product'),
      'alt_text' => $this->faker->sentence(),
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

