<?php

namespace Modules\Order\Repositories\Interface\Order;

use Modules\Order\Entities\Order\Order;
use Illuminate\Support\Collection;

interface ReadOrderRepositoryInterface
{
    public function show(int $orderId): Order;

    public function getAll(): Collection;

    public function findById(int $id): Order;

    public function isDelivered(int $orderId): bool;

    public function count(): int;

    public function countByStatus(string $status): int;

    public function countCouponUsage(int $couponId): int;

    public function countUserCouponUsage(int $couponId, int $userId): int;
}


