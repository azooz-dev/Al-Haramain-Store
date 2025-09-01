<?php

namespace Database\Factories\Review;

use App\Models\Review\Review;
use App\Models\User\User;
use App\Models\Product\Product;
use App\Models\Order\Order;
use Illuminate\Database\Eloquent\Factories\Factory;
use Database\Factories\User\UserFactory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Review\Review>
 */
class ReviewFactory extends Factory
{
  /**
   * Define the model's default state.
   *
   * @return array<string, mixed>
   */
  public function definition(): array
  {
    return [
      'user_id' => UserFactory::new(),
      'product_id' => Product::factory(),
      'order_id' => Order::factory(),
      'rating' => fake()->numberBetween(1, 5),
      'comment' => fake()->paragraph(),
      'status' => fake()->randomElement([Review::PENDING, Review::APPROVED, Review::REJECTED]),
      'locale' => fake()->randomElement(['en', 'ar']),
    ];
  }

  /**
   * Indicate that the review is pending.
   */
  public function pending(): static
  {
    return $this->state(fn(array $attributes) => [
      'status' => Review::PENDING,
    ]);
  }

  /**
   * Indicate that the review is approved.
   */
  public function approved(): static
  {
    return $this->state(fn(array $attributes) => [
      'status' => Review::APPROVED,
    ]);
  }

  /**
   * Indicate that the review is rejected.
   */
  public function rejected(): static
  {
    return $this->state(fn(array $attributes) => [
      'status' => Review::REJECTED,
    ]);
  }

  /**
   * Indicate that the review is in English.
   */
  public function english(): static
  {
    return $this->state(fn(array $attributes) => [
      'locale' => 'en',
    ]);
  }

  /**
   * Indicate that the review is in Arabic.
   */
  public function arabic(): static
  {
    return $this->state(fn(array $attributes) => [
      'locale' => 'ar',
    ]);
  }
}
