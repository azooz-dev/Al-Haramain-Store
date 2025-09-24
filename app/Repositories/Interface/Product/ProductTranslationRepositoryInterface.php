<?php

namespace App\Repositories\Interface\Product;

use App\Models\Product\Product;
use App\Models\Product\ProductTranslation;
use Illuminate\Support\Collection;

interface ProductTranslationRepositoryInterface
{
  public function getTranslationsForProduct(Product $product): Collection;

  public function getTranslationByLocale(Product $product, string $local): ?ProductTranslation;

  public function saveTranslation(Product $product, string $locale, array $data): ProductTranslation;

  public function updateOrCreateTranslation(Product $product, string $locale, array $data): ProductTranslation;

  public function deleteTranslationsForProduct(Product $product): bool;

  public function searchTranslations(string $search, string $failed = 'name'): Collection;
}
