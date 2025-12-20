<?php

use Modules\Order\Enums\OrderStatus;

return [
    'status_transitions' => [
        OrderStatus::PENDING->value => [
            OrderStatus::PROCESSING->value,
            OrderStatus::SHIPPED->value,
            OrderStatus::CANCELLED->value,
        ],
        OrderStatus::PROCESSING->value => [
            OrderStatus::SHIPPED->value,
            OrderStatus::CANCELLED->value,
        ],
        OrderStatus::SHIPPED->value => [
            OrderStatus::DELIVERED->value,
            OrderStatus::CANCELLED->value,
        ],
        OrderStatus::DELIVERED->value => [
            OrderStatus::REFUNDED->value,
        ],
        OrderStatus::CANCELLED->value => [],
        OrderStatus::REFUNDED->value => [],
    ],
];
