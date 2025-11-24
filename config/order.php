<?php

return [
    'status_transitions' => [
        \App\Models\Order\Order::PENDING => [
            \App\Models\Order\Order::PROCESSING,
            \App\Models\Order\Order::SHIPPED,
            \App\Models\Order\Order::CANCELLED,
        ],
        \App\Models\Order\Order::PROCESSING => [
            \App\Models\Order\Order::SHIPPED,
            \App\Models\Order\Order::CANCELLED,
        ],
        \App\Models\Order\Order::SHIPPED => [
            \App\Models\Order\Order::DELIVERED,
            \App\Models\Order\Order::CANCELLED,
        ],
        \App\Models\Order\Order::DELIVERED => [
            \App\Models\Order\Order::REFUNDED,
        ],
        \App\Models\Order\Order::CANCELLED => [],
        \App\Models\Order\Order::REFUNDED => [],
    ],
];


