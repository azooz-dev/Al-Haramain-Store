<?php

namespace App\Repositories\Eloquent\Product;

use App\Models\Product\Product;
use App\Models\Product\ProductTranslation;
use App\Repositories\Interface\Product\ProductTranslationRepositoryInterface;
use Illuminate\Support\Collection;

class ProductTranslationRepository implements ProductTranslationRepositoryInterface
{
  public function getTranslationsForProduct(Product $product): Collection
  {
    return $product->translations;
  }

  public function getTranslationByLocale(Product $product, string $local): ?ProductTranslation
  {
    return $product->translations()->where('local', $local)->first();
  }

  public function saveTranslation(Product $product, string $locale, array $data): ProductTranslation
  {
    return $product->translations()->create([...$data, 'local' => $locale]);
  }

  public function updateOrCreateTranslation(Product $product, string $locale, array $data): ProductTranslation
  {
    return ProductTranslation::updateOrCreate(
      [
        ...$data
      ],
      [
        'product_id' => $product->id,
        'local' => $locale
      ]
    );
  }

  public function deleteTranslationsForProduct(Product $product): bool
  {
    return $product->translations()->delete();
  }

  public function searchTranslations(string $search, string $failed = 'name'): Collection
  {
    return ProductTranslation::where($failed, 'like', "%{$search}%")->get();
  }
}
