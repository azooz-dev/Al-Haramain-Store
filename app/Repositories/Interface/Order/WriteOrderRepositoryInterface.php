<?php

namespace App\Repositories\Interface\Order;

use App\Models\Order\Order;

interface WriteOrderRepositoryInterface
{
    public function store(array $data): Order;

    public function update(int $id, array $data): Order;

    public function delete(int $id): bool;

    public function updateStatus(int $id, string $status): Order;
}


