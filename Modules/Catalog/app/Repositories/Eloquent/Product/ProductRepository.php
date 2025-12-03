<?php

namespace Modules\Catalog\Repositories\Eloquent\Product;

use Modules\Catalog\Entities\Product\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Modules\Catalog\Repositories\Interface\Product\ProductRepositoryInterface;
use Illuminate\Support\Collection;

class ProductRepository implements ProductRepositoryInterface
{
  public function getAllProducts()
  {
    return Product::with(['translations', 'colors', 'colors.images', 'colors.variants'])->get();
  }

  public function findById(int $id): ?Product
  {
    return Product::with(['translations', 'colors', 'colors.images', 'colors.variants'])->findOrFail($id);
  }

  public function findByIdWithTranslations(int $id): ?Product
  {
    return Product::with('translations')->findOrFail($id);
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

  public function create(array $data): Product
  {
    return Product::create($data);
  }

  public function update(int $id, array $data): Product
  {
    $product = Product::findOrFail($id);
    $product->update($data);
    return $product->fresh(['translations', 'colors.images', 'variants', 'categories.translations']);
  }

  public function delete(int $id): bool
  {
    $product = Product::findOrFail($id);
    return $product->delete();
  }

  public function count(): int
  {
    return Product::count();
  }

  public function getQueryBuilder(): Builder
  {
    return Product::query()
      ->withoutGlobalScopes([SoftDeletingScope::class])
      ->with(['translations', 'colors.images', 'variants', 'categories.translations'])
      ->withCount(['colors', 'variants', 'images', 'categories']);
  }

  public function decrementProductStock(int $productId, int $quantity): bool
  {
    return Product::where('id', $productId)
      ->where('quantity', '>=', $quantity)
      ->decrement('quantity', $quantity) > 0;
  }
}


