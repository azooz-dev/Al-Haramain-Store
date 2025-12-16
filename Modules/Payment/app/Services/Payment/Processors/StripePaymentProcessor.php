<?php

namespace Modules\Payment\Services\Payment\Processors;

use Carbon\Carbon;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use Stripe\PaymentMethod;
use Modules\Payment\DTOs\PaymentResult;
use Modules\Payment\Contracts\PaymentProcessorInterface;
use Modules\Payment\Exceptions\Payment\VerifyPaymentException;
use Modules\Payment\Exceptions\Payment\ProcessPaymentException;
use Modules\Payment\Exceptions\Payment\CreatePaymentIntentException;

class StripePaymentProcessor implements PaymentProcessorInterface
{
  public function __construct()
  {
    Stripe::setApiKey(config('services.stripe.secret'));
  }

  public function requiresPaymentIntent(): bool
  {
    return true;
  }

  public function createPaymentIntent(array $orderData): ?string
  {
    try {
      $intent = PaymentIntent::create([
        'amount' => (int) ($orderData['total_amount'] * 100),
        'currency' => 'usd',
        'payment_method_types' => ['card'],
        'metadata' => [
          'user_id' => $orderData['user_id'],
          'address_id' => $orderData['address_id'],
          'coupon_code' => $orderData['coupon_code'] ?? null,
          'items' => json_encode($orderData['items']),
          'payment_method' => $orderData['payment_method'],
          'total_amount' => $orderData['total_amount'],
        ],
      ]);
      return $intent->client_secret;
    } catch (\Exception $e) {
      throw new CreatePaymentIntentException($e->getMessage(), 500);
    }
  }

  public function processPayment(array $orderData): PaymentResult
  {
    // This is called after payment is confirmed on frontend
    $paymentIntentId = $orderData['payment_intent_id'];

    if (!$paymentIntentId) {
      throw new ProcessPaymentException(__('app.messages.payment.payment_intent_id_required'), 500);
    }

    return $this->verifyPayment($paymentIntentId);
  }

  public function verifyPayment(string $paymentIntentId): PaymentResult
  {
    try {
      $paymentIntent = PaymentIntent::retrieve($paymentIntentId, [
        'expand' => ['payment_method', 'charges.data.payment_method'],
      ]);

      if ($paymentIntent->status !== 'succeeded') {
        throw new VerifyPaymentException(__('app.messages.payment.payment_intent_not_succeeded'), 500);
      }

      $paymentMethod = $paymentIntent->payment_method;

      if (is_string($paymentMethod)) {
        $paymentMethod = PaymentMethod::retrieve($paymentMethod);
      }

      // Extract card details
      $cardDetails = $paymentMethod->card;

      return new PaymentResult(
        success: true,
        transactionId: $paymentIntent->id,
        paymentMethod: $cardDetails->brand,
        amount: $paymentIntent->amount_received / 100,
        paidAt: Carbon::createFromTimestamp($paymentIntent->created)
      );
    } catch (VerifyPaymentException $e) {
      throw new VerifyPaymentException($e->getMessage(), 500);
    }
  }
}
