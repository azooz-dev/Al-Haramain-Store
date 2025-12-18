<?php

namespace Modules\Order\Services\Order\Pipeline;

use Modules\User\Contracts\UserServiceInterface;
use Modules\Order\Exceptions\Order\OrderException;

class ValidateBuyerStep implements OrderProcessingStep
{
    public function __construct(private UserServiceInterface $userService) {}

    public function handle(array $data, \Closure $next)
    {
        $buyer = $this->userService->findUserById($data['user_id']);
        
        if (!$buyer) {
            throw new OrderException(__('app.messages.order.buyer_not_found'), 404);
        }
        
        if (!$buyer->verified) {
            throw new OrderException(__('app.messages.order.buyer_not_verified'), 403);
        }

        return $next($data);
    }
}


