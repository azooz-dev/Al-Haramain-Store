<?php

namespace Modules\Catalog\Database\Factories\Category;

use Modules\Catalog\Entities\Category\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Modules\Catalog\Entities\Category\Category>
 */
class CategoryFactory extends Factory
{
  /**
   * The name of the factory's corresponding model.
   *
   * @var class-string<\Illuminate\Database\Eloquent\Model>
   */
  protected $model = Category::class;

  /**
   * Define the model's default state.
   *
   * @return array<string, mixed>
   */
  public function definition(): array
  {
    return [
      'slug' => fake()->unique()->slug(),
      'image' => fake()->imageUrl(640, 480, 'category'),
    ];
  }

  /**
   * Indicate a specific slug.
   */
  public function slug(string $slug): static
  {
    return $this->state(fn(array $attributes) => [
      'slug' => $slug,
    ]);
  }
}

