<?php

namespace Modules\Order\Http\Controllers\Order;

use App\Http\Controllers\Controller;
use Modules\Order\Http\Requests\Order\OrderRequest;
use Modules\Order\Contracts\OrderServiceInterface;

use function App\Helpers\showOne;
use function App\Helpers\showAll;

class OrderController extends Controller
{
    public function __construct(private OrderServiceInterface $orderService) {}
    
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Pass authenticated user ID explicitly to ensure correct context
        $userId = auth('sanctum')->id() ?? auth()->id();
        $orders = $this->orderService->getUserOrders($userId);

        return showAll($orders, 'Orders', 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(OrderRequest $request)
    {
        $order = $this->orderService->storeOrder($request->validated());

        return showOne($order, 'order', 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(int $orderId)
    {
        $order = $this->orderService->findOrderById($orderId);

        // Check if user owns the order
        if ($order->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        return showOne($order, 'Order', 200);
    }
}
