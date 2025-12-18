<?php

namespace Modules\Catalog\Services\Product;

use Modules\Catalog\Http\Resources\Product\ProductApiResource;
use Modules\Catalog\Entities\Product\Product;
use Modules\Catalog\Repositories\Interface\Product\ProductRepositoryInterface;
use Modules\Catalog\Repositories\Interface\Product\ProductTranslationRepositoryInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class ProductTranslationService
{

  public function __construct(
    private ProductRepositoryInterface $productRepository,
    private ProductTranslationRepositoryInterface $translationRepository,
  ) {}


  public function getTranslationsForProduct(Product $product): Collection
  {
    return $this->translationRepository->getTranslationsForProduct($product);
  }

  public function getTranslatedName(Product $product, ?string $locale = null)
  {
    $locale = $locale ?: app()->getLocale();

    $translation = $this->translationRepository->getTranslationByLocale($product, $locale);

    if (!$translation) {
      $translation = $this->translationRepository->getTranslationByLocale($product, 'en');
    }

    return $translation->name ?? "";
  }

  public function getTranslatedDescription(Product $product, $locale = null)
  {
    $locale = $locale ?: app()->getLocale();

    $translation = $this->translationRepository->getTranslationByLocale($product, $locale);

    if (!$translation) {
      $translation = $this->translationRepository->getTranslationByLocale($product, 'en');
    }

    return $translation->description ?? '';
  }

  public function saveTranslation(Product $product, array $translationDate)
  {
    foreach (['en', 'ar'] as $locale) {
      $payload = $translationDate[$locale];

      if (!empty($payload['name']) || !empty($payload['description'])) {
        $this->translationRepository->updateOrCreateTranslation($product, $locale, $payload);
      }
    }
  }

  public function getFormData(Product $product): array
  {
    return (new ProductApiResource($product->load('translations')))->toArray(request());
  }

  public function findProductWithTranslations(int $id): ?Product
  {
    return $this->productRepository->findByIdWithTranslations($id);
  }

  public function searchProductsByName(string $search): Collection
  {
    return $this->productRepository->searchByName($search);
  }

  public function generateUniqueSlug(string $name): string
  {
    $baseSlug = Str::slug($name, '-');
    $slug = $baseSlug;
    $counter = 1;

    while ($this->slugExists($slug)) {
      $slug = $baseSlug . '-' . $counter;
      $counter++;
    }

    return $slug;
  }

  public function generateSlugFromName(string $productName): string
  {
    return $this->generateUniqueSlug($productName);
  }

  public function slugExists(string $slug): bool
  {
    return $this->productRepository->slugExists($slug);
  }
}


