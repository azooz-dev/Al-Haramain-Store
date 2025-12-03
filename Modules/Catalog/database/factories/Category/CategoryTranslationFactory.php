<?php

namespace Modules\Catalog\Database\Factories\Category;

use Modules\Catalog\Entities\Category\Category;
use Modules\Catalog\Entities\Category\CategoryTranslation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Modules\Catalog\Entities\Category\CategoryTranslation>
 */
class CategoryTranslationFactory extends Factory
{
  /**
   * The name of the factory's corresponding model.
   *
   * @var class-string<\Illuminate\Database\Eloquent\Model>
   */
  protected $model = CategoryTranslation::class;

  /**
   * Define the model's default state.
   *
   * @return array<string, mixed>
   */
  public function definition(): array
  {
    return [
      'category_id' => Category::factory(),
      'local' => fake()->randomElement(['en', 'ar']),
      'name' => fake()->words(2, true),
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

