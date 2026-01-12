<?php

namespace Modules\Coupon\Database\Factories\Coupon;

use Modules\Coupon\Entities\Coupon\Coupon;
use Modules\Coupon\Enums\CouponType;
use Modules\Coupon\Enums\CouponStatus;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\DB;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Modules\Coupon\Entities\Coupon\Coupon>
 */
class CouponFactory extends Factory
{
  /**
   * The name of the factory's corresponding model.
   *
   * @var class-string<\Illuminate\Database\Eloquent\Model>
   */
  protected $model = Coupon::class;

  /**
   * Define the model's default state.
   *
   * @return array<string, mixed>
   */
  public function definition(): array
  {
    $type = $this->faker->randomElement(CouponType::cases());
    $discountAmount = $type === CouponType::FIXED
      ? $this->faker->randomFloat(2, 5, 100)
      : $this->faker->numberBetween(5, 50);

    // Use ONLY UUID for guaranteed global uniqueness
    // UUIDs are cryptographically unique and don't rely on timestamps or counters
    // This is the most reliable approach for test factories with RefreshDatabase
    $uniqueCode = 'TEST-' . \Illuminate\Support\Str::uuid()->toString();

    return [
      'code' => $uniqueCode,
      'name' => $this->faker->words(2, true),
      'type' => $type,
      'discount_amount' => $discountAmount,
      'usage_limit' => $this->faker->numberBetween(10, 1000),
      'usage_limit_per_user' => $this->faker->numberBetween(1, 5),
      'start_date' => $this->faker->dateTimeBetween('-1 month', '+1 month'),
      'end_date' => $this->faker->dateTimeBetween('+1 month', '+6 months'),
      'status' => $this->faker->randomElement(CouponStatus::cases()),
    ];
  }

  /**
   * Indicate that the coupon is active.
   */
  public function active(): static
  {
    return $this->state(fn(array $attributes) => [
      'status' => CouponStatus::ACTIVE,
      'start_date' => $this->faker->dateTimeBetween('-1 month', 'now'),
      'end_date' => $this->faker->dateTimeBetween('now', '+6 months'),
    ]);
  }

  /**
   * Indicate that the coupon is inactive.
   */
  public function inactive(): static
  {
    return $this->state(fn(array $attributes) => [
      'status' => CouponStatus::INACTIVE,
    ]);
  }

  /**
   * Indicate a fixed discount type.
   */
  public function fixedDiscount(): static
  {
    return $this->state(fn(array $attributes) => [
      'type' => CouponType::FIXED,
      'discount_amount' => $this->faker->randomFloat(2, 5, 100),
    ]);
  }

  /**
   * Indicate a percentage discount type.
   */
  public function percentageDiscount(): static
  {
    return $this->state(fn(array $attributes) => [
      'type' => CouponType::PERCENTAGE,
      'discount_amount' => $this->faker->numberBetween(5, 50),
    ]);
  }

  /**
   * Indicate that the coupon is expired.
   */
  public function expired(): static
  {
    return $this->state(fn(array $attributes) => [
      'status' => CouponStatus::ACTIVE,
      'start_date' => $this->faker->dateTimeBetween('-6 months', '-2 months'),
      'end_date' => $this->faker->dateTimeBetween('-2 months', '-1 day'),
    ]);
  }
}
