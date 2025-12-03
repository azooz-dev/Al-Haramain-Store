<?php

namespace Modules\Catalog\Repositories\Interface\Product;

use Modules\Catalog\Entities\Product\Product;
use Illuminate\Support\Collection;

interface ReadProductRepositoryInterface
{
    public function getAllProducts();

    public function findById(int $id): ?Product;

    public function findByIdWithTranslations(int $id): ?Product;

    public function searchByName(string $search): Collection;

    public function slugExists(string $slug): bool;

    public function count(): int;
}


