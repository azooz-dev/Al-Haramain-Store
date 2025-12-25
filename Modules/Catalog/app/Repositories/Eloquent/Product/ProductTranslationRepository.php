<?php

namespace Modules\Catalog\Repositories\Eloquent\Product;

use Modules\Catalog\Entities\Product\Product;
use Modules\Catalog\Entities\Product\ProductTranslation;
use Modules\Catalog\Repositories\Interface\Product\ProductTranslationRepositoryInterface;
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
        'product_id' => $product->id,
        'local' => $locale,
      ],
      [
        'name' => $data['name'] ?? '',
        'description' => $data['description'] ?? '',
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


