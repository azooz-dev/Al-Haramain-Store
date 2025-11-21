<?php

namespace App\Services\Product;

use App\Models\Product\Product;
use Illuminate\Database\Eloquent\Builder;
use function App\Helpers\errorResponse;
use App\Exceptions\Product\ProductException;
use App\Http\Resources\Product\ProductApiResource;
use App\Repositories\Interface\Product\ProductRepositoryInterface;
use App\Repositories\Interface\Product\Color\ProductColorRepositoryInterface;
use App\Repositories\Interface\Product\Variant\ProductVariantRepositoryInterface;

class ProductService
{
  public function __construct(
    private ProductRepositoryInterface $productRepository,
    private ProductColorRepositoryInterface $productColorRepository,
    private ProductVariantRepositoryInterface $productVariantRepository,
    private ProductTranslationService $translationService
  ) {}

  public function getProducts()
  {
    try {
      $products = $this->productRepository->getAllProducts();

      return ProductApiResource::collection($products);
    } catch (ProductException $e) {
      return errorResponse($e->getMessage(), 500);
    }
  }

  public function findProductById(int $id)
  {
    try {
      $product = $this->getProductById($id);

      return new ProductApiResource($product);
    } catch (ProductException $e) {
      return errorResponse($e->getMessage(), 500);
    }
  }

  public function getProductById(int $id)
  {
    return $this->productRepository->findById($id);
  }

  public function checkColorBelongsToProduct(int $productId, int $colorId)
  {
    return $this->productColorRepository->colorBelongsToProduct($productId, $colorId);
  }

  public function checkVariantBelongsToProductAndColor(int $productId, int $colorId, int $variantId)
  {
    return $this->productVariantRepository->variantBelongsToProductAndColor($productId, $colorId, $variantId);
  }

  public function createProduct(array $data, array $translationData, ?array $categoryIds = null): Product
  {
    // Generate unique slug from English name if not provided
    if (empty($data['slug']) && !empty($translationData['en']['name'])) {
      $data['slug'] = $this->translationService->generateSlugFromName($translationData['en']['name']);
    }

    // Create product via repository
    $product = $this->productRepository->create($data);

    // Save translations via ProductTranslationService
    $this->translationService->saveTranslation($product, $translationData);

    // Sync categories if provided
    if ($categoryIds !== null) {
      $product->categories()->sync($categoryIds);
    }

    // Return product with relationships loaded
    return $product->fresh(['translations', 'colors.images', 'variants', 'categories.translations']);
  }

  public function updateProduct(int $id, array $data, array $translationData, ?array $categoryIds = null): Product
  {
    $product = $this->productRepository->findByIdWithTranslations($id);

    // Check if English name changed
    $currentEnglishName = $this->translationService->getTranslatedName($product, 'en');
    $newEnglishName = $translationData['en']['name'] ?? null;

    // If name changed, regenerate slug
    if ($newEnglishName && $newEnglishName !== $currentEnglishName) {
      $data['slug'] = $this->translationService->generateSlugFromName($newEnglishName);
    }

    // Update product via repository
    $product = $this->productRepository->update($id, $data);

    // Update translations via ProductTranslationService
    $this->translationService->saveTranslation($product, $translationData);

    // Sync categories if provided
    if ($categoryIds !== null) {
      $product->categories()->sync($categoryIds);
    }

    // Return updated product with relationships loaded
    return $product->fresh(['translations', 'colors.images', 'variants', 'categories.translations']);
  }

  public function deleteProduct(int $id): bool
  {
    return $this->productRepository->delete($id);
  }

  public function getProductsCount(): int
  {
    return $this->productRepository->count();
  }

  public function getQueryBuilder(): Builder
  {
    return $this->productRepository->getQueryBuilder();
  }

  public function getPriceRange(Product $product): string
  {
    // Use model accessor
    return $product->price_range;
  }
}
