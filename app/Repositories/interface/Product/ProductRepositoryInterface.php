<?php

namespace App\Repositories\interface\Product;

use App\Models\Product\Product;
use Illuminate\Support\Collection;

interface ProductRepositoryInterface
{
  public function findById(int $id): ?Product;

  public function findByIdWithTranslations(int $id): ?Product;

  public function getAllWithTranslations(): Collection;

  public function create(array $data): ?Product;

  public function update(Product $product, array $data): bool;

  public function delete(Product $product): bool;

  public function searchByName(string $search): Collection;

  public function slugExists(string $slug): bool;
}
