<?php

namespace Modules\Payment\Http\Requests\Payment;

use Modules\Order\Http\Requests\Order\BaseOrderRequest;

class CreatePaymentIntentRequest extends BaseOrderRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return array_merge($this->getCommonRules(), [
            'total_amount' => 'required|numeric|min:0',
        ]);
    }
}
