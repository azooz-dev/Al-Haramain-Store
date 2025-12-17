<?php

namespace Modules\Coupon\Database\Factories\Coupon;

use Modules\Coupon\Entities\Coupon\Coupon;
use Modules\Coupon\Entities\Coupon\CouponUser;
use Modules\User\Entities\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Modules\Coupon\Entities\Coupon\CouponUser>
 */
class CouponUserFactory extends Factory
{
  /**
   * Define the model's default state.
   *
   * @return array<string, mixed>
   */
  public function definition(): array
  {
    return [
      'coupon_id' => Coupon::factory(),
      'user_id' => User::factory(),
      'times_used' => fake()->numberBetween(0, 5),
    ];
  }

  /**
   * Indicate that the coupon has been used multiple times.
   */
  public function usedMultipleTimes(): static
  {
    return $this->state(fn(array $attributes) => [
      'times_used' => fake()->numberBetween(1, 5),
    ]);
  }

  /**
   * Indicate that the coupon has never been used.
   */
  public function unused(): static
  {
    return $this->state(fn(array $attributes) => [
      'times_used' => 0,
    ]);
  }
}
