<?php

namespace Database\Factories\Coupon;

use App\Models\Coupon\Coupon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Coupon\Coupon>
 */
class CouponFactory extends Factory
{
  /**
   * Define the model's default state.
   *
   * @return array<string, mixed>
   */
  public function definition(): array
  {
    $type = fake()->randomElement([Coupon::FIXED, Coupon::PERCENTAGE]);
    $discountAmount = $type === Coupon::FIXED
      ? fake()->randomFloat(2, 5, 100)
      : fake()->numberBetween(5, 50);

    return [
      'code' => fake()->unique()->regexify('[A-Z]{3}[0-9]{3}'),
      'name' => fake()->words(2, true),
      'type' => $type,
      'discount_amount' => $discountAmount,
      'usage_limit' => fake()->numberBetween(10, 1000),
      'usage_limit_per_user' => fake()->numberBetween(1, 5),
      'start_date' => fake()->dateTimeBetween('-1 month', '+1 month'),
      'end_date' => fake()->dateTimeBetween('+1 month', '+6 months'),
      'status' => fake()->randomElement([Coupon::ACTIVE, Coupon::INACTIVE]),
    ];
  }

  /**
   * Indicate that the coupon is active.
   */
  public function active(): static
  {
    return $this->state(fn(array $attributes) => [
      'status' => Coupon::ACTIVE,
      'start_date' => fake()->dateTimeBetween('-1 month', 'now'),
      'end_date' => fake()->dateTimeBetween('now', '+6 months'),
    ]);
  }

  /**
   * Indicate that the coupon is inactive.
   */
  public function inactive(): static
  {
    return $this->state(fn(array $attributes) => [
      'status' => Coupon::INACTIVE,
    ]);
  }

  /**
   * Indicate a fixed discount type.
   */
  public function fixedDiscount(): static
  {
    return $this->state(fn(array $attributes) => [
      'type' => Coupon::FIXED,
      'discount_amount' => fake()->randomFloat(2, 5, 100),
    ]);
  }

  /**
   * Indicate a percentage discount type.
   */
  public function percentageDiscount(): static
  {
    return $this->state(fn(array $attributes) => [
      'type' => Coupon::PERCENTAGE,
      'discount_amount' => fake()->numberBetween(5, 50),
    ]);
  }
}
