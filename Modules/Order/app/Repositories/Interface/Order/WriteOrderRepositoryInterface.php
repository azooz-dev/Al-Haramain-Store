<?php

namespace Modules\Order\Repositories\Interface\Order;

use Modules\Order\Entities\Order\Order;

interface WriteOrderRepositoryInterface
{
    public function store(array $data): Order;

    public function update(int $id, array $data): Order;

    public function delete(int $id): bool;

    public function updateStatus(int $id, string $status): Order;

    public function markOrdersAsProcessing(array $ids): int;

    public function markOrdersAsShipped(array $ids): int;
}


