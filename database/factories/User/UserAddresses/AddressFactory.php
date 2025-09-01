<?php

namespace Database\Factories\User\UserAddresses;

use App\Models\User\User;
use App\Models\User\UserAddresses\Address;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User\UserAddresses\Address>
 */
class AddressFactory extends Factory
{
  /**
   * Define the model's default state.
   *
   * @return array<string, mixed>
   */
  public function definition(): array
  {
    return [
      'user_id' => User::factory(),
      'full_name' => fake()->name(),
      'phone' => fake()->phoneNumber(),
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
