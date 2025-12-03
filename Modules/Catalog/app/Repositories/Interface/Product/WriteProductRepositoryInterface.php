<?php

namespace Modules\Catalog\Repositories\Interface\Product;

use Modules\Catalog\Entities\Product\Product;

interface WriteProductRepositoryInterface
{
    public function create(array $data): Product;

    public function update(int $id, array $data): Product;

    public function delete(int $id): bool;

    public function decrementProductStock(int $productId, int $quantity): bool;
}


