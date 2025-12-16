<?php

namespace Modules\Order\Database\Factories\Order;

use Modules\User\Entities\User;
use Modules\Order\Entities\Order\Order;
use Modules\Coupon\Entities\Coupon\Coupon;
use Modules\User\Entities\Address;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Modules\Order\Entities\Order\Order>
 */
class OrderFactory extends Factory
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
      'coupon_id' => fake()->optional(0.3)->randomElement([Coupon::factory()]),
      'address_id' => Address::factory(),
      'order_number' => 'ORD-' . fake()->unique()->numberBetween(1000, 9999),
      'total_amount' => fake()->randomFloat(2, 50, 2000),
      'payment_method' => fake()->randomElement([Order::PAYMENT_METHOD_CASH_ON_DELIVERY, Order::PAYMENT_METHOD_CREDIT_CARD]),
      'status' => fake()->randomElement(Order::getStatuses()),
    ];
  }

  /**
   * Indicate that the order is pending.
   */
  public function pending(): static
  {
    return $this->state(fn(array $attributes) => [
      'status' => Order::PENDING,
    ]);
  }

  /**
   * Indicate that the order is processing.
   */
  public function processing(): static
  {
    return $this->state(fn(array $attributes) => [
      'status' => Order::PROCESSING,
    ]);
  }

  /**
   * Indicate that the order is shipped.
   */
  public function shipped(): static
  {
    return $this->state(fn(array $attributes) => [
      'status' => Order::SHIPPED,
    ]);
  }

  /**
   * Indicate that the order is delivered.
   */
  public function delivered(): static
  {
    return $this->state(fn(array $attributes) => [
      'status' => Order::DELIVERED,
    ]);
  }

  /**
   * Indicate that the order is cancelled.
   */
  public function cancelled(): static
  {
    return $this->state(fn(array $attributes) => [
      'status' => Order::CANCELLED,
    ]);
  }

  /**
   * Indicate that the order is refunded.
   */
  public function refunded(): static
  {
    return $this->state(fn(array $attributes) => [
      'status' => Order::REFUNDED,
    ]);
  }

  /**
   * Indicate cash on delivery payment method.
   */
  public function cashOnDelivery(): static
  {
    return $this->state(fn(array $attributes) => [
      'payment_method' => Order::PAYMENT_METHOD_CASH_ON_DELIVERY,
    ]);
  }

  /**
   * Indicate credit card payment method.
   */
  public function creditCard(): static
  {
    return $this->state(fn(array $attributes) => [
      'payment_method' => Order::PAYMENT_METHOD_CREDIT_CARD,
    ]);
  }
}
