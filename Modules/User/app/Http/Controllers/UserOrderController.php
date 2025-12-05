<?php

namespace Modules\User\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\User\Order\UserOrderService;

use function App\Helpers\showAll;

class UserOrderController extends Controller
{
    public function __construct(private UserOrderService $userOrderService) {}
    /**
     * Display a listing of the resource.
     */
    public function index(int $userId)
    {
        $orders = $this->userOrderService->getAllUserOrders($userId);

        return showAll($orders, 'User Orders', 200);
    }
}
