<?php

namespace App\Repositories\Interface\Product;

use App\Models\Product\Product;
use Illuminate\Support\Collection;

interface ProductRepositoryInterface
{
  public function getAllProducts();

  public function findById(int $id): ?Product;

  public function findByIdWithTranslations(int $id): ?Product;

  public function searchByName(string $search): Collection;

  public function slugExists(string $slug): bool;
}
