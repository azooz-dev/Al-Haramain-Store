<?php

namespace Modules\Payment\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use Modules\Payment\Http\Requests\Payment\CreatePaymentIntentRequest;
use Modules\Payment\Contracts\PaymentServiceInterface;

use function App\Helpers\showOne;

class PaymentController extends Controller
{
    public function __construct(private PaymentServiceInterface $paymentService) {}


    public function createPaymentIntent(CreatePaymentIntentRequest $request)
    {
        $data = $request->validated();
        $clientSecret = $this->paymentService->createPaymentIntent($data);

        return showOne(['client_secret' => $clientSecret]);
    }
}
