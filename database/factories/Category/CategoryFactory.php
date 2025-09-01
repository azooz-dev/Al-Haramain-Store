<?php

namespace Database\Factories\Category;

use App\Models\Category\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category\Category>
 */
class CategoryFactory extends Factory
{
  /**
   * Define the model's default state.
   *
   * @return array<string, mixed>
   */
  public function definition(): array
  {
    $name = fake()->words(2, true);

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
