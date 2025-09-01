<?php

namespace Database\Factories\Payment;

use App\Models\Order\Order;
use App\Models\Payment\Payment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Payment\Payment>
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
    $status = fake()->randomElement([Payment::PENDING, Payment::PAID, Payment::FAILED, Payment::REFUNDED]);
    $paidAt = $status === Payment::PAID ? fake()->dateTimeBetween('-1 year', 'now') : null;

    return [
      'order_id' => Order::factory(),
      'payment_method' => fake()->randomElement(['credit_card', 'paypal', 'stripe', 'cash_on_delivery']),
      'transaction_id' => fake()->unique()->uuid(),
      'amount' => fake()->randomFloat(2, 10, 1000),
      'status' => $status,
      'paid_at' => $paidAt,
    ];
  }

  /**
   * Indicate that the payment is pending.
   */
  public function pending(): static
  {
    return $this->state(fn(array $attributes) => [
      'status' => Payment::PENDING,
      'paid_at' => null,
    ]);
  }

  /**
   * Indicate that the payment is paid.
   */
  public function paid(): static
  {
    return $this->state(fn(array $attributes) => [
      'status' => Payment::PAID,
      'paid_at' => fake()->dateTimeBetween('-1 year', 'now'),
    ]);
  }

  /**
   * Indicate that the payment failed.
   */
  public function failed(): static
  {
    return $this->state(fn(array $attributes) => [
      'status' => Payment::FAILED,
      'paid_at' => null,
    ]);
  }

  /**
   * Indicate that the payment was refunded.
   */
  public function refunded(): static
  {
    return $this->state(fn(array $attributes) => [
      'status' => Payment::REFUNDED,
      'paid_at' => fake()->dateTimeBetween('-1 year', '-1 month'),
    ]);
  }
}
