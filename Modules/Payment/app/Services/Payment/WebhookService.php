<?php

namespace Modules\Payment\Services\Payment;

use Stripe\PaymentIntent;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Order\Contracts\OrderServiceInterface;
use Modules\Payment\Contracts\WebhookServiceInterface;
use Modules\Payment\Repositories\Interface\Payment\PaymentRepositoryInterface;

class WebhookService implements WebhookServiceInterface
{
  public function __construct(
    private OrderServiceInterface $orderService,
    private PaymentRepositoryInterface $paymentRepository
  ) {}

  public function handlePaymentSucceeded(PaymentIntent $paymentIntent): void
  {
    $paymentIntentId = $paymentIntent->id;

    // Check if payment record already exists (order was created via normal flow)
    $existingPayment = $this->paymentRepository->findByTransactionId($paymentIntentId);

    if ($existingPayment) {
      Log::info('Payment already exists for transaction ID: ' . $paymentIntentId, [
        'payment_intent_id' => $paymentIntentId,
        'order_id' => $existingPayment->order_id,
      ]);

      return;
    }

    $this->recreateOrderFromMetadata($paymentIntent);
  }

  public function handlePaymentFailed(PaymentIntent $paymentIntent): void
  {
    Log::info('Payment failed for transaction ID: ' . $paymentIntent->id, [
      'payment_intent_id' => $paymentIntent->id,
      'error' => $paymentIntent->last_payment_error ?? null
    ]);
  }

  public function handlePaymentCanceled(PaymentIntent $paymentIntent): void
  {
    Log::info('Payment canceled for transaction ID: ' . $paymentIntent->id, [
      'payment_intent_id' => $paymentIntent->id,
    ]);
  }

  public function recreateOrderFromMetadata(PaymentIntent $paymentIntent): void
  {
    $metadata = $paymentIntent->metadata;

    // Validate required metadata
    if (empty($metadata['user_id']) || empty($metadata['address_id']) || empty($metadata['items'])) {
      Log::error('Missing required metadata for order recreation', [
        'payment_intent_id' => $paymentIntent->id,
        'metadata' => $metadata,
      ]);
      return;
    }

    DB::transaction(
      function () use ($paymentIntent, $metadata) {
        // Reconstruct order data from metadata
        $orderData = [
          'user_id' => (int) $metadata['user_id'],
          'address_id' => (int) $metadata['address_id'],
          'coupon_code' => $metadata['coupon_code'] ?? null,
          'items' => json_decode($metadata['items'], true),
          'payment_method' => $metadata['payment_method'] ?? 'credit_card',
          'payment_intent_id' => $paymentIntent->id,
          'total_amount' => (float) ($metadata['total_amount'] ?? ($paymentIntent->amount / 100)),
        ];

        // Create order using existing OrderService
        $order = $this->orderService->storeOrder($orderData);

        Log::info('Order recreated from metadata', [
          'order_id' => $order->id,
          'payment_intent_id' => $paymentIntent->id,
        ]);
      },
      5
    );
  }
}
