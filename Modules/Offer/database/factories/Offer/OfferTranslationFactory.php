<?php

namespace Modules\Offer\Database\Factories\Offer;

use Modules\Offer\Entities\Offer\Offer;
use Modules\Offer\Entities\Offer\OfferTranslation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Modules\Offer\Entities\Offer\OfferTranslation>
 */
class OfferTranslationFactory extends Factory
{
  /**
   * The name of the factory's corresponding model.
   *
   * @var class-string<\Illuminate\Database\Eloquent\Model>
   */
  protected $model = OfferTranslation::class;
  /**
   * Define the model's default state.
   *
   * @return array<string, mixed>
   */
  public function definition(): array
  {
    return [
      'offer_id' => Offer::factory(),
      'locale' => $this->faker->randomElement(['en', 'ar']),
      'name' => $this->faker->words(2, true),
      'description' => $this->faker->sentence(10, false),
    ];
  }

  /**
   * Indicate that the translation is in English.
   */
  public function english(): static
  {
    return $this->state(fn(array $attributes) => [
      'locale' => 'en',
    ]);
  }

  /**
   * Indicate that the translation is in Arabic.
   */
  public function arabic(): static
  {
    return $this->state(fn(array $attributes) => [
      'locale' => 'ar',
    ]);
  }
}
