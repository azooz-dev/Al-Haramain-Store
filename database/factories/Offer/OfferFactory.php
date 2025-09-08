<?php

namespace Database\Factories\Offer;

use App\Models\Offer\Offer;
use App\Models\Product\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Offer\Offer>
 */
class OfferFactory extends Factory
{
  /**
   * Define the model's default state.
   *
   * @return array<string, mixed>
   */
  public function definition(): array
  {
    $discountType = fake()->randomElement([Offer::FIXED, Offer::PERCENTAGE]);
    $discountAmount = $discountType === Offer::FIXED
      ? fake()->randomFloat(2, 5, 100)
      : fake()->numberBetween(5, 50);

    return [
      'image_path' => fake()->imageUrl(640, 480, 'offer'),
      'discount_type' => $discountType,
      'discount_amount' => $discountAmount,
      'start_date' => fake()->dateTimeBetween('-1 month', '+1 month'),
      'end_date' => fake()->dateTimeBetween('+1 month', '+3 months'),
      'status' => fake()->randomElement([Offer::ACTIVE, Offer::INACTIVE]),
    ];
  }

  /**
   * Indicate that the offer is active.
   */
  public function active(): static
  {
    return $this->state(fn(array $attributes) => [
      'status' => Offer::ACTIVE,
      'start_date' => fake()->dateTimeBetween('-1 month', 'now'),
      'end_date' => fake()->dateTimeBetween('now', '+3 months'),
    ]);
  }

  /**
   * Indicate that the offer is inactive.
   */
  public function inactive(): static
  {
    return $this->state(fn(array $attributes) => [
      'status' => Offer::INACTIVE,
    ]);
  }

  /**
   * Indicate a fixed discount type.
   */
  public function fixedDiscount(): static
  {
    return $this->state(fn(array $attributes) => [
      'discount_type' => Offer::FIXED,
      'discount_amount' => fake()->randomFloat(2, 5, 100),
    ]);
  }

  /**
   * Indicate a percentage discount type.
   */
  public function percentageDiscount(): static
  {
    return $this->state(fn(array $attributes) => [
      'discount_type' => Offer::PERCENTAGE,
      'discount_amount' => fake()->numberBetween(5, 50),
    ]);
  }
}
