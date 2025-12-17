<?php

namespace Modules\Offer\Database\Factories\Offer;

use Modules\Offer\Entities\Offer\Offer;
use Modules\Catalog\Entities\Product\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Modules\Offer\Entities\Offer\Offer>
 */
class OfferFactory extends Factory
{
  /**
   * The name of the factory's corresponding model.
   *
   * @var class-string<\Illuminate\Database\Eloquent\Model>
   */
  protected $model = Offer::class;
  /**
   * Define the model's default state.
   *
   * @return array<string, mixed>
   */
  public function definition(): array
  {
    $products_total_price = fake()->randomFloat(2, 100, 1000);
    $discount_amount = fake()->randomFloat(2, 10, 200);
    $offer_price = $products_total_price - $discount_amount;

    return [
      'image_path' => fake()->imageUrl(640, 480, 'offer'),
      'products_total_price' => $products_total_price,
      'offer_price' => $offer_price,
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
}
