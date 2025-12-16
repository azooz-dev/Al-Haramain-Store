<?php

namespace App\Http\Controllers\Payment;

use Stripe\Webhook;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use function App\Helpers\errorResponse;
use function App\Helpers\successResponse;

use App\Services\Payment\WebhookService;
use Stripe\Exception\SignatureVerificationException;

class StripeWebhookController extends Controller
{
    public function __construct(private WebhookService $webhookService) {}

    public function handle(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $webhookSecret = config('services.stripe.webhook_secret');

        try {
            $event = Webhook::constructEvent($payload, $sigHeader, $webhookSecret);
        } catch (SignatureVerificationException $e) {
            return errorResponse($e->getMessage(), 400);
        }

        switch ($event->type) {
            case 'payment_intent.succeeded':
                $this->webhookService->handlePaymentSucceeded($event->data->object);
                break;
            case 'payment_intent.payment_failed':
                $this->webhookService->handlePaymentFailed($event->data->object);
                break;
            case 'payment_intent.canceled':
                $this->webhookService->handlePaymentCanceled($event->data->object);
                break;
            default:
                Log::warning('Unsupported event type: ' . $event->type);
                break;
        }

        return successResponse(__('app.messages.payment.webhook_processed'));
    }
}
