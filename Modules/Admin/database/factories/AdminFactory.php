<?php

namespace Modules\Admin\Database\Factories;

use Modules\Admin\Entities\Admin;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Modules\Admin\Entities\Admin>
 */
class AdminFactory extends Factory
{
  /**
   * The name of the factory's corresponding model.
   *
   * @var class-string<\Illuminate\Database\Eloquent\Model>
   */
  protected $model = Admin::class;
  /**
   * The current password being used by the factory.
   */
  protected static ?string $password;

  /**
   * Define the model's default state.
   *
   * @return array<string, mixed>
   */
  public function definition(): array
  {
    return [
      'name' => $this->faker->name(),
      'email' => $this->faker->unique()->safeEmail(),
      'phone' => $this->faker->phoneNumber(),
      'verified' => $this->faker->boolean(90),
      'email_verified_at' => now(),
      'password' => static::$password ??= Hash::make('password'),
    ];
  }

  /**
   * Indicate that the admin's email address should be unverified.
   */
  public function unverified(): static
  {
    return $this->state(fn(array $attributes) => [
      'email_verified_at' => null,
    ]);
  }

  /**
   * Indicate that the admin is verified.
   */
  public function verified(): static
  {
    return $this->state(fn(array $attributes) => [
      'verified' => true,
      'email_verified_at' => now(),
    ]);
  }
}
