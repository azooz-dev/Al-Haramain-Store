<?php

namespace Modules\Catalog\Repositories\Interface\Product;

use Modules\Catalog\Entities\Product\Product;
use Modules\Catalog\Entities\Product\ProductTranslation;
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


