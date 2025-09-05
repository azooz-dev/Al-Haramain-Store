<?php

namespace App\Http\Controllers\Order;

use App\Http\Controllers\Controller;
use App\Http\Requests\Order\OrderRequest;
use App\Services\Order\OrderService;

class OrderController extends Controller
{
    public function __construct(private OrderService $orderService) {}
    /**
     * Store a newly created resource in storage.
     */
    public function store(OrderRequest $request)
    {
        return $this->orderService->storeOrder($request->validated());
    }
}
