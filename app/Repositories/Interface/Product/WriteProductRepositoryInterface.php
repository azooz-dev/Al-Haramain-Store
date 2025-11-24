<?php

namespace App\Repositories\Interface\Product;

use App\Models\Product\Product;

interface WriteProductRepositoryInterface
{
    public function create(array $data): Product;

    public function update(int $id, array $data): Product;

    public function delete(int $id): bool;
}


