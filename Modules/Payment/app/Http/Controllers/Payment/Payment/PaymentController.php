<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Http\Requests\Payment\CreatePaymentIntentRequest;
use App\Services\Payment\PaymentService;

use function App\Helpers\showOne;

class PaymentController extends Controller
{
    public function __construct(private PaymentService $paymentService) {}


    public function createPaymentIntent(CreatePaymentIntentRequest $request)
    {
        $data = $request->validated();
        $clientSecret = $this->paymentService->createPaymentIntent($data);

        return showOne(['client_secret' => $clientSecret]);
    }
}
