<?php

namespace Modules\Review\Database\Factories\Review;

use Modules\Review\Entities\Review\Review;
use Modules\Review\Enums\ReviewStatus;
use Modules\User\Entities\User;
use Modules\Catalog\Entities\Product\Product;
use Modules\Order\Entities\Order\Order;
use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\User\Database\Factories\UserFactory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Modules\Review\Entities\Review\Review>
 */
class ReviewFactory extends Factory
{
  /**
   * The name of the factory's corresponding model.
   *
   * @var class-string<\Illuminate\Database\Eloquent\Model>
   */
  protected $model = Review::class;

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
      'status' => fake()->randomElement(ReviewStatus::cases()),
      'locale' => fake()->randomElement(['en', 'ar']),
    ];
  }

  /**
   * Indicate that the review is pending.
   */
  public function pending(): static
  {
    return $this->state(fn(array $attributes) => [
      'status' => ReviewStatus::PENDING,
    ]);
  }

  /**
   * Indicate that the review is approved.
   */
  public function approved(): static
  {
    return $this->state(fn(array $attributes) => [
      'status' => ReviewStatus::APPROVED,
    ]);
  }

  /**
   * Indicate that the review is rejected.
   */
  public function rejected(): static
  {
    return $this->state(fn(array $attributes) => [
      'status' => ReviewStatus::REJECTED,
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
