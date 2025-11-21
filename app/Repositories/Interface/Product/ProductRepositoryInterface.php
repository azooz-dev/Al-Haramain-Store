<?php

namespace App\Repositories\Interface\Product;

use App\Models\Product\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

interface ProductRepositoryInterface
{
  public function getAllProducts();

  public function findById(int $id): ?Product;

  public function findByIdWithTranslations(int $id): ?Product;

  public function searchByName(string $search): Collection;

  public function slugExists(string $slug): bool;

  public function create(array $data): Product;

  public function update(int $id, array $data): Product;

  public function delete(int $id): bool;

  public function count(): int;

  public function getQueryBuilder(): Builder;
}
