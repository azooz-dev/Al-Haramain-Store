<?php

namespace Modules\User\Database\Factories;

use Modules\User\Entities\User;
use Modules\User\Entities\Address;
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
      'label' => fake()->words(2, true),
      'address_type' => fake()->randomElement([Address::ADDRESS_TYPE_HOME, Address::ADDRESS_TYPE_WORK, Address::ADDRESS_TYPE_OTHER]),
      'street' => fake()->streetAddress(),
      'city' => fake()->city(),
      'state' => fake()->state(),
      'postal_code' => fake()->postcode(),
      'country' => fake()->country(),
      'is_default' => fake()->boolean(20),
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
