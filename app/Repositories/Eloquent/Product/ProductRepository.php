<?php

namespace App\Repositories\Eloquent\Product;

use App\Models\Product\Product;
use App\Repositories\interface\Product\ProductRepositoryInterface;
use Illuminate\Support\Collection;

class ProductRepository implements ProductRepositoryInterface
{
  public function getAllProducts()
  {
    return Product::with(['translations', 'colors', 'colors.images', 'colors.variants'])->get();
  }

  public function findById(int $id): ?Product
  {
    return Product::with(['translations', 'colors', 'colors.images', 'colors.variants'])->find($id);
  }

  public function findByIdWithTranslations(int $id): ?Product
  {
    return Product::with('translations')->find($id);
  }

  public function searchByName(string $search): Collection
  {
    return Product::whereHas('translations', function ($query) use ($search) {
      $query->where('name', 'like', "%{$search}%");
    })->with('translations')->get();
  }

  public function slugExists(string $slug): bool
  {
    return Product::where('slug', $slug)->exists();
  }
}
