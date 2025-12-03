<?php

namespace Modules\Catalog\Database\Factories\Product;

use Modules\Catalog\Entities\Product\Product;
use Modules\Catalog\Entities\Product\ProductTranslation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Modules\Catalog\Entities\Product\ProductTranslation>
 */
class ProductTranslationFactory extends Factory
{
  /**
   * The name of the factory's corresponding model.
   *
   * @var class-string<\Illuminate\Database\Eloquent\Model>
   */
  protected $model = ProductTranslation::class;

  /**
   * Define the model's default state.
   *
   * @return array<string, mixed>
   */
  public function definition(): array
  {
    return [
      'product_id' => Product::factory(),
      'local' => fake()->randomElement(['en', 'ar']),
      'name' => fake()->words(3, true),
      'description' => fake()->paragraph(),
    ];
  }

  /**
   * Indicate that the translation is in English.
   */
  public function english(): static
  {
    return $this->state(fn(array $attributes) => [
      'local' => 'en',
    ]);
  }

  /**
   * Indicate that the translation is in Arabic.
   */
  public function arabic(): static
  {
    return $this->state(fn(array $attributes) => [
      'local' => 'ar',
    ]);
  }
}

