<?php

namespace App\Http\Controllers\Order;

use App\Http\Controllers\Controller;
use App\Http\Requests\Order\OrderRequest;
use App\Services\Order\OrderService;

use function App\Helpers\showOne;

class OrderController extends Controller
{
    public function __construct(private OrderService $orderService) {}
    /**
     * Store a newly created resource in storage.
     */
    public function store(OrderRequest $request)
    {
        $order = $this->orderService->storeOrder($request->validated());

        return showOne($order, 'order', 201);
    }

    /**
     * Display a listing of the resource.
     */
    public function show(int $orderId)
    {
        $order = $this->orderService->findOrderById($orderId);

        return showOne($order, 'Order', 200);
    }
}
