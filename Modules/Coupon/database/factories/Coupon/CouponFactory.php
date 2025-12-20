<?php

namespace Modules\Coupon\Database\Factories\Coupon;

use Modules\Coupon\Entities\Coupon\Coupon;
use Modules\Coupon\Enums\CouponType;
use Modules\Coupon\Enums\CouponStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

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

  // Static counter to ensure uniqueness across all factory calls
  private static $counter = 0;

  /**
   * Define the model's default state.
   *
   * @return array<string, mixed>
   */
  public function definition(): array
  {
    $type = fake()->randomElement(CouponType::cases());
    $discountAmount = $type === CouponType::FIXED
      ? fake()->randomFloat(2, 5, 100)
      : fake()->numberBetween(5, 50);

    // Generate unique code only if not provided
    self::$counter++;
    $uniqueCode = 'TEST-' . str_pad((string)self::$counter, 8, '0', STR_PAD_LEFT) . '-' . bin2hex(random_bytes(8));

    return [
      'code' => $uniqueCode,
      'name' => fake()->words(2, true),
      'type' => $type,
      'discount_amount' => $discountAmount,
      'usage_limit' => fake()->numberBetween(10, 1000),
      'usage_limit_per_user' => fake()->numberBetween(1, 5),
      'start_date' => fake()->dateTimeBetween('-1 month', '+1 month'),
      'end_date' => fake()->dateTimeBetween('+1 month', '+6 months'),
      'status' => fake()->randomElement(CouponStatus::cases()),
    ];
  }

  /**
   * Indicate that the coupon is active.
   */
  public function active(): static
  {
    return $this->state(fn(array $attributes) => [
      'status' => CouponStatus::ACTIVE,
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
      'discount_amount' => fake()->randomFloat(2, 5, 100),
    ]);
  }

  /**
   * Indicate a percentage discount type.
   */
  public function percentageDiscount(): static
  {
    return $this->state(fn(array $attributes) => [
      'type' => CouponType::PERCENTAGE,
      'discount_amount' => fake()->numberBetween(5, 50),
    ]);
  }
}
