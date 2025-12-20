<?php

namespace Modules\Payment\Database\Factories\Payment;

use Modules\Order\Entities\Order\Order;
use Modules\Payment\Entities\Payment\Payment;
use Modules\Payment\Enums\PaymentStatus;
use Modules\Payment\Enums\PaymentMethod;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Modules\Payment\Entities\Payment\Payment>
 */
class PaymentFactory extends Factory
{
  /**
   * Define the model's default state.
   *
   * @return array<string, mixed>
   */
  public function definition(): array
  {
    $status = fake()->randomElement([PaymentStatus::SUCCESS, PaymentStatus::FAILED]);
    $paidAt = $status === PaymentStatus::SUCCESS ? fake()->dateTimeBetween('-1 year', 'now') : null;

    return [
      'order_id' => Order::factory(),
      'payment_method' => fake()->randomElement(PaymentMethod::cases()),
      'transaction_id' => fake()->unique()->uuid(),
      'amount' => fake()->randomFloat(2, 10, 1000),
      'status' => $status,
      'paid_at' => $paidAt,
    ];
  }

  /**
   * Indicate that the payment is successful.
   */
  public function successful(): static
  {
    return $this->state(fn(array $attributes) => [
      'status' => PaymentStatus::SUCCESS,
      'paid_at' => fake()->dateTimeBetween('-1 year', 'now'),
    ]);
  }

  /**
   * Indicate that the payment failed.
   */
  public function failed(): static
  {
    return $this->state(fn(array $attributes) => [
      'status' => PaymentStatus::FAILED,
      'paid_at' => null,
    ]);
  }
}
