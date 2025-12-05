<?php

namespace App\Services\Order\Pipeline;

use Modules\User\Entities\User;
use App\Exceptions\Order\OrderException;

class ValidateBuyerStep implements OrderProcessingStep
{
    public function handle(array $data, \Closure $next)
    {
        $buyer = User::find($data['user_id']);
        
        if (!$buyer) {
            throw new OrderException(__('app.messages.order.buyer_not_found'), 404);
        }
        
        if (!$buyer->verified) {
            throw new OrderException(__('app.messages.order.buyer_not_verified'), 403);
        }

        return $next($data);
    }
}


