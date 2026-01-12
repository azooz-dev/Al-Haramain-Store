<?php

namespace Modules\User\Database\Factories;

use Modules\User\Entities\User;
use Modules\User\Entities\Address;
use Modules\User\Enums\AddressType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Modules\User\Entities\Address>
 */
class AddressFactory extends Factory
{
  /**
   * The name of the factory's corresponding model.
   *
   * @var class-string<\Illuminate\Database\Eloquent\Model>
   */
  protected $model = Address::class;

  /**
   * Define the model's default state.
   *
   * @return array<string, mixed>
   */
  public function definition(): array
  {
    return [
      'user_id' => User::factory(),
      'label' => $this->faker->words(2, true),
      'address_type' => $this->faker->randomElement(AddressType::cases()),
      'street' => $this->faker->streetAddress(),
      'city' => $this->faker->city(),
      'state' => $this->faker->state(),
      'postal_code' => $this->faker->postcode(),
      'country' => $this->faker->country(),
      'is_default' => $this->faker->boolean(20),
    ];
  }

  /**
   * Indicate that the address is the default address.
   */
  public function default(): static
  {
    return $this->state(fn(array $attributes) => [
      'is_default' => true,
    ]);
  }

  /**
   * Indicate that the address is not the default address.
   */
  public function notDefault(): static
  {
    return $this->state(fn(array $attributes) => [
      'is_default' => false,
    ]);
  }
}
