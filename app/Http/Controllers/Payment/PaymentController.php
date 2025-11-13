<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Http\Requests\Payment\CreatePaymentIntentRequest;
use App\Services\Payment\PaymentService;

class PaymentController extends Controller
{
    public function __construct(private PaymentService $paymentService) {}


    public function createPaymentIntent(CreatePaymentIntentRequest $request)
    {
        $data = $request->validated();

        return $this->paymentService->createPaymentIntent($data);
    }
}
