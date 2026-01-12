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
   * The name of the factory's corresponding model.
   *
   * @var class-string<\Illuminate\Database\Eloquent\Model>
   */
  protected $model = Payment::class;

  /**
   * Define the model's default state.
   *
   * @return array<string, mixed>
   */
  public function definition(): array
  {
    $status = $this->faker->randomElement([PaymentStatus::SUCCESS, PaymentStatus::FAILED]);
    $paidAt = $status === PaymentStatus::SUCCESS ? $this->faker->dateTimeBetween('-1 year', 'now') : null;

    return [
      'order_id' => Order::factory(),
      'payment_method' => $this->faker->randomElement(PaymentMethod::cases()),
      'transaction_id' => $this->faker->unique()->uuid(),
      'amount' => $this->faker->randomFloat(2, 10, 1000),
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
      'paid_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
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
