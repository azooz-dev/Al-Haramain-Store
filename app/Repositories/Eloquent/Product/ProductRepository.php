<?php

namespace App\Repositories\Eloquent\Product;

use App\Models\Product\Product;
use App\Repositories\interface\Product\ProductRepositoryInterface;
use Illuminate\Support\Collection;

class ProductRepository implements ProductRepositoryInterface
{
  public function findById(int $id): ?Product
  {
    return Product::find($id);
  }

  public function findByIdWithTranslations(int $id): ?Product
  {
    return Product::with('translations')->find($id);
  }

  public function getAllWithTranslations(): Collection
  {
    return Product::with('translations')->get();
  }

  public function create(array $data): ?Product
  {
    return Product::create($data);
  }

  public function update(Product $product, array $data): bool
  {
    return $product->update($data);
  }

  public function delete(Product $product): bool
  {
    return $product->delete();
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

  public function slugExistsExcluding(string $slug, int $excluded): bool
  {
    return Product::where('slug', $slug)->where('id', '!=', $excluded)->exists;
  }
}
