<?php

return [
    'status_transitions' => [
        \Modules\Order\Entities\Order\Order::PENDING => [
            \Modules\Order\Entities\Order\Order::PROCESSING,
            \Modules\Order\Entities\Order\Order::SHIPPED,
            \Modules\Order\Entities\Order\Order::CANCELLED,
        ],
        \Modules\Order\Entities\Order\Order::PROCESSING => [
            \Modules\Order\Entities\Order\Order::SHIPPED,
            \Modules\Order\Entities\Order\Order::CANCELLED,
        ],
        \Modules\Order\Entities\Order\Order::SHIPPED => [
            \Modules\Order\Entities\Order\Order::DELIVERED,
            \Modules\Order\Entities\Order\Order::CANCELLED,
        ],
        \Modules\Order\Entities\Order\Order::DELIVERED => [
            \Modules\Order\Entities\Order\Order::REFUNDED,
        ],
        \Modules\Order\Entities\Order\Order::CANCELLED => [],
        \Modules\Order\Entities\Order\Order::REFUNDED => [],
    ],
];


